<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ){}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $alertsStatus = $this->security->getUser()->getAlertsStatus();

        $builder
            ->add('alertsStatus', CheckboxType::class, [
                'label' => 'Do you want to activate email alerts?',
                'label_attr' => [
                    'class' => 'mr-2'
                ],
                'attr' => [
                    'class' => 'bg-blue-950 hover:bg-blue-900 text-white flex items-center gap-2 p-2 uppercase font-bold text-sm px-4 py-3 ',
                    'checked' => $alertsStatus
                ],
                'row_attr' => [
                    'class' => 'flex justify-start my-8'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Save',
                'attr' => [
                    'class' => 'bg-blue-950 hover:bg-blue-900 text-white flex items-center gap-2 p-2 uppercase font-bold text-sm px-4 py-3 ',
                ],
                'row_attr' => [
                    'class' => 'flex justify-start my-8'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
