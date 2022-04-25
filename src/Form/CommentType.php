<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('body', TextareaType::class, [
                'required' => true,
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Длина комментария должна быть больше {{ limit }} символов!',
                        'max' => 255,
                        'maxMessage' => 'Длина комментария должна быть не более {{ limit }} символов!',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Отправить',
                'attr' => ['class' => 'btn btn-primary w-100 mt-2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
