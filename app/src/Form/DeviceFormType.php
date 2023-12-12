<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DeviceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('device_id', TextType::class,[
                'label' => 'Identificador',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => 'host01'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'Etiqueta en minúsculas que identifique el nuevo dispositivo',
                'help_attr' => [
                    'class'=> 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('entity_name', TextType::class,[
                'label' => 'Nombre',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => 'urn:ngsi-ld:Host:001'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'Nombre de la entidad ngsi-ld relacionada con el dispositivo',
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
                'help' => 'Tipo de la entidad ngsi-ld relacionada con el dispositivo',
                'help_attr' => [
                    'class'=> 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('attributes', TextareaType::class,[
                'label' => 'Atributos',
                'label_attr' => [
                    'class' => 'font-semibold text-gray-400'
                ],
                'attr' => [
                    'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                    'placeholder' => 's,status,Boolean'
                ],
                'row_attr' => [
                    'class' => 'flex flex-col my-4'
                ],
                'help' => 'Un atributo por línea y cada parámetro del atributo serparado por comas',
                'help_attr' => [
                    'class'=> 'text-xs text-gray-300 p-1'
                ]
            ])
            ->add('send', SubmitType::class,[
                'label' => 'Crear dispositivo',
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
