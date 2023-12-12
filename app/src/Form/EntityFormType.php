<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EntityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', TextType::class,[
            'label' => 'Identificador',
            'label_attr' => [
                'class' => 'font-semibold text-gray-400'
            ],
            'attr' => [
                'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                'placeholder' => 'urn:ngsi-ld:Website:001'
            ],
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'Identificador de la entidad en formato ngsi-ld',
            'help_attr' => [
                'class'=> 'text-xs text-gray-300 p-1'
            ]
        ])
        ->add('type', TextType::class,[
            'label' => 'Tipo',
            'label_attr' => [
                'class' => 'font-semibold text-gray-400'
            ],
            'attr' => [
                'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                'placeholder' => 'Website'
            ],
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'Nombre de la entidad',
            'help_attr' => [
                'class'=> 'text-xs text-gray-300 p-1'
            ]
        ])
        ->add('fields', TextareaType::class,[
            'label' => 'Tipo',
            'label_attr' => [
                'class' => 'font-semibold text-gray-400'
            ],
            'attr' => [
                'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                'placeholder' => 'status,Boolean,false'
            ],
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'Campos de la entidad por línea separados sus parámetros (name,type,value) por comas',
            'help_attr' => [
                'class'=> 'text-xs text-gray-300 p-1'
            ]
        ])
        ->add('send', SubmitType::class,[
            'label' => 'Crear entidad',
            'attr' => [
                'class' => 'bg-blue-950 hover:bg-blue-900 text-white flex items-center gap-2 p-2 uppercase font-bold text-sm px-4 py-3 ',
            ],
            'row_attr' => [
                'class' => 'flex justify-center my-8'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
