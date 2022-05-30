<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\DependencyInjection\ContainerInterface;
//use Symfony\Config\Security;


class RegistrationFormType extends AbstractType
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $rolesArr = $this->rolesTransformer(
            $this->container->getParameter('security.role_hierarchy.roles')
        );

        $builder
            ->add('email', EmailType::class, [
                'attr' => array('class' => 'form-control')
            ])
            ->add('roles', ChoiceType::class, array(
                    'attr'  =>  ['class' => 'form-control', 'style' => 'margin:5px 0;', 'size' => 8],
                    'choices' => $rolesArr,
                    'multiple' => true,
                    'required' => true,
                )
            )
            ->add('agreeTerms', CheckboxType::class, [
                'label_attr' => ['class' => 'form-check-label'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'attr' => array('class' => 'form-check-input mx-2')
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function rolesTransformer(array $securityRoles)
    {
        $mappedRoles = [];

        foreach ($securityRoles as $roleKey => $rolesArr) {
            $roleKey = str_replace('ROLE_', '', trim($roleKey));
            $roleKey = ucwords(strtolower(str_replace('_', ' ', $roleKey)));

            $m = [];

            foreach ($rolesArr as $k => $v) {
                $m[$v] = $v;
            }

            $mappedRoles[$roleKey] = $m;
        }

        return $mappedRoles;
    }
}
