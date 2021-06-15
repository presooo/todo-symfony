<?php


namespace App\Presentation\Form;


class UpdateTask
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        // Builds the form

        $builder
            ->add('id', HiddenType::class, [
                'empty_data' => $options['id'] ?? $options['id']
            ])
            ->add('status', TextType::class, [
                'attr' => ['maxlength' => 191, ],
            ])
            ->add('description', TextType::class, [
                'attr' => ['maxlength' => 191, ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // Sets the form defaults

        $resolver->setDefaults([
            'data_class'  => \App\Application\Command\Task\UpdateTask::class
        ]);
    }
}
