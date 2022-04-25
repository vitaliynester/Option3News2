<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Имя пользователя должно быть не менее {{ limit }} символов!',
                        'max' => 255,
                        'maxMessage' => 'Имя пользователя должно быть максимум {{ limit }} символов!',
                    ]),
                ],
                'label' => 'Имя пользователя',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите электронную почту!',
                    ]),
                    new Email([
                        'message' => 'Некорректный Email!',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Данная почта не подходит. Ограничение по символам {{ limit }} максимум!',
                    ])
                ],
                'label' => 'Электронная почта',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Необходимо согласиться с использованием персональных данных!',
                    ]),
                ],
                'label' => 'Я согласен с использованием моих персональных данных',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'required' => true,
                'mapped' => false,
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Минимальная длина пароля {{ limit }} символов',
                        'max' => 4096,
                    ]),
                ],
                'first_options' => [
                    'label' => 'Пароль',
                ],
                'second_options' => [
                    'label' => 'Повторите пароль'
                ],
                'invalid_message' => 'Пароли не совпадают!',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
