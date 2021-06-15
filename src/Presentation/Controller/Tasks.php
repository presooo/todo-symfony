<?php


namespace App\Presentation\Controller;

use App\Application\Command\CommandBus;
use App\Domain\ReadModel\Repository\TasksRepository;
use App\Presentation\Form\UpdateTask;
use App\Presentation\OAuthSecuredController;
use App\Application\Command\Task\UpdateTask as UpdateTaskCommand;


/**
 *
 * @Route(
 *     "/v1",
 *     name="api.tasks.",
 *     host="api.{domain}",
 *     defaults={"domain"="%domain%"},
 *     requirements={"domain"="%domain%"}
 * )
 */
class Tasks extends OAuthSecuredController
{

    private CommandBus $commandBus;
    private TasksRepository $tasksRepository;


    public function __construct(
        TasksRepository $tasksRepository,
        FormErrorRetriever $errorRetriever,
        CommandBus $commandBus
    ) {
        $this->tasksRepository = $tasksRepository;
        $this->commandBus      = $commandBus;

        parent::__construct($errorRetriever);
    }


    /**
     * @Route("/tasks", methods={"GET", "HEAD", "OPTIONS"}, name="taskList")
     */
    public function taskList(Request $request) : Response
    {

        $this->requireScope($request, Scope::SCOPE_READ_TASKS);

        $userID = $this->getUserIDFromRequest($request);

        $filter = new TaskFilter($this->getJsonParametersFromBody($request));

        $tasks = $this->tasksRepository->tasks($userID, $filter);

        return new JsonResponse(
            $tasks->toSerialisable(),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }


    /**
     * @Route("/tasks", methods={"POST"}, name="createTask")
     */
    public function createTask(Request $request) : Response
    {
        $this->requireScope($request, Scope::SCOPE_WRITE_ROOMS);

        $newId        = TaskId::generate();
        $userId       = $this->getUserIDFromRequest($request);

        $form = $this->createForm(
            CreateTask::class,
            null,
            [
                'user_id'         => $userId,
                'csrf_protection' => false,
            ]
        );

        $this->submitFormAndHandleCommand($form, $request, $this->commandBus);

        return new Response(
            null,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'api.tasks.byId',
                    ['id' => $newId->toString()]
                )
            ]
        );
    }


    /**
     * @Route("/tasks/{taskId}/update", methods={"PUT"}, name="update", requirements={"taskId"="%uuid%"})
     */
    public function update(Request $request, string $callId) : Response
    {
        $this->requireScope($request, Scope::SCOPE_WRITE_TASKS);

        $data = $this->getJsonParametersFromBody($request);

        $form = $this->createForm(
            UpdateTask::class,
            new UpdateTaskCommand($data),
            [
                'callId'          => $callId,
                'csrf_protection' => false
            ]
        );

        $this->submitFormAndHandleCommand($form, $request, $this->commandBus, []);

        return new Response(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Location' => $this->generateUrl(
                    'api.tasks.byId',
                    ['callId' => $callId]
                )
            ]
        );
    }


    /**
     * @Route("/tasks/{taskId}", methods={"GET"}, name="taskById", requirements={"taskId"="%uuid%"})
     */
    public function taskById(Request $request, string $taskId) : Response
    {

        $this->requireScope($request, Scope::SCOPE_READ_TASKS);

        $tasks = $this->tasksRepository->tasksById(TaskId::fromString($taskId));

        return new JsonResponse(
            $tasks->toSerialisable(),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
