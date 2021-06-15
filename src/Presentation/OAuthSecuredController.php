<?php


namespace App\Presentation;


use App\Application\Command\CommandBus;
use App\Domain\Exception\InvalidFormValues;
use App\Presentation\FormErrorRetriever;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


abstract class OAuthSecuredController extends AbstractController implements AccessTokenAuthenticated
{
    /** @var FormErrorRetriever */
    private $formErrorRetriever;

    public function __construct(FormErrorRetriever $formErrorRetriever)
    {
        $this->formErrorRetriever = $formErrorRetriever;
    }

    protected function requireScope(Request $request, string $scope) : void
    {
        if (! $request->headers->has('X-Scopes')) {
            throw new BadRequestHttpException('Scopes missing from request');
        }

        if (! in_array($scope, explode(' ', $request->headers->get('X-Scopes')), true)) {
            throw new AccessDeniedHttpException('Insufficient permission');
        }
    }

    protected function getUserIDFromRequest(Request $request) : UserId
    {
        if (! $request->headers->has('X-UserID')) {
            throw new BadRequestHttpException('UserID missing from request');
        }

        if (! is_string($request->headers->get('X-UserID'))) {
            throw new BadRequestHttpException('UserID must be a string');
        }

        return UserId::fromString($request->headers->get('X-UserID'));
    }

    protected function getJsonParametersFromBody(Request $request) : array
    {
        $body = $request->getContent(false);

        if (strlen($body) === 0) {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Body does not contain valid JSON: ' . json_last_error_msg());
        }

        $decoded = $this->convertBooleansToIntStrings($decoded);

        return $decoded;
    }

    protected function submitFormAndHandleCommand(
        FormInterface $form,
        Request $request,
        CommandBus $commandBus,
        array $parametersToStrip = []
    ) : void
    {
        $parameters = $this->getJsonParametersFromBody($request);

        if (! empty($parametersToStrip)) {
            foreach ($parametersToStrip as $parameterName) {
                if (isset($parameters[$parameterName])) {
                    unset($parameters[$parameterName]);
                }
            }
        }

        $form->submit($parameters);

        if ($form->isValid()) {
            $command = $form->getData();

            $commandBus->handle($command);
        } else {
            $errors = $this->formErrorRetriever->getFlattenedErrorsFromFormAsStringArray($form);

            throw InvalidFormValues::invalidValues(...$errors);
        }
    }
}

