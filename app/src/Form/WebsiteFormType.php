<?php

namespace App\Form;

use App\Service\IoTAgentService;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WebsiteFormType extends AbstractType
{
    public function __construct(
        private readonly IoTAgentService $ioTAgentService,
        private readonly Security $security,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $organizations = [];
        $services = json_decode($this->ioTAgentService->getServices($this->security->getUser()->getUserIdentifier(), '/*'),true)['services'];
        foreach ($services as $key => $service) {
            $name = $service['static_attributes'][0]['value'];
            $apikey = $service['apikey'];
            $organizations[$name] = $name;
        }

        $builder
        ->add('name', TextType::class, [
            'label' => 'Name',
            'label_attr' => [
                'class' => 'font-semibold text-gray-400'
            ],
            'attr' => [
                'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                'placeholder' => 'Example.org'
            ],
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'A name for the web site',
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
                'placeholder' => 'This domain is for use in illustrative examples in documents.'
            ],
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'A description of the web site',
            'help_attr' => [
                'class' => 'text-xs text-gray-300 p-1'
            ]
        ])
        ->add('organization', ChoiceType::class, [
            'label' => 'Organization',
            'label_attr' => [
                'class' => 'font-semibold text-gray-400'
            ],
            'attr' => [
                'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
            ],
            'placeholder' => 'Select an organization',
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'Select an organization under which site will be classified',
            'help_attr' => [
                'class' => 'text-xs text-gray-300 p-1'
            ],
            'choices' => $organizations
        ])
        ->add('url', UrlType::class, [
            'label' => 'Site URL',
            'label_attr' => [
                'class' => 'font-semibold text-gray-400'
            ],
            'attr' => [
                'class' => 'border border-gray-300 text-gray-500 p-2 placeholder-gray-300',
                'placeholder' => 'https://example.org'
            ],
            'row_attr' => [
                'class' => 'flex flex-col my-4'
            ],
            'help' => 'The url of the web site',
            'help_attr' => [
                'class' => 'text-xs text-gray-300 p-1'
            ]
        ])
        ->add('send', SubmitType::class, [
            'label' => 'Create website',
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
