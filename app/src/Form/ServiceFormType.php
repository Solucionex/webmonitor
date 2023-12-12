<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ServiceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apikey', TextType::class,[
                'label' => 'Clave API',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'Puede utilizar la generada por defecto o agregar cualquier cadena de alfanumérica',
                'help_attr' => [
                    'class'=> 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('entity_type', TextType::class,[
                'label' => 'Tipo',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => 'Host'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'Tipo de la entidad ngsi-ld con la que está relacionada el servicio',
                'help_attr' => [
                    'class'=> 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('resource', TextType::class,[
                'label' => 'Clave API',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => '/iot/d'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'Ruta del servicio',
                'help_attr' => [
                    'class'=> 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('send', SubmitType::class,[
                'label' => 'Crear servicio',
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
