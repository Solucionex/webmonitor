<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrganizationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => 'Acme'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'A name for the organization',
                'help_attr' => [
                    'class' => 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => 'A fictional corporation'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'A description of the organization',
                'help_attr' => [
                    'class' => 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Create organization',
                'attr' => [
                    'class' => 'bg-blue-950 hover:bg-blue-900 text-white flex items-center gap-2 p-2 uppercase font-bold text-sm px-4 py-3 ',
                ],
                'row_attr' => [
                    'class' => 'flex justify-center my-8'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
