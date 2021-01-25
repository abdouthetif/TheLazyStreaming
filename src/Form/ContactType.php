<?php

namespace App\Form;

use App\Controller\ContactController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => 'true',
                'label' => 'Nom'
            ])
            ->add('email', EmailType::class, [
                'required' => 'true',
                'label' => 'Email'
            ])
            ->add('objet', TextType::class, [
                'required' => 'true',
                'label' => 'Objet'
            ])
            ->add('message', TextareaType::class, [
                'required' => 'true',
                'label' => 'Message'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data class' => ContactController::class,
        ]);
    }
}
