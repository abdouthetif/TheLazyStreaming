<?php

namespace App\Form;

use App\Controller\ForgottenMdpController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfirmCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', NumberType::class, [
                'required' => 'true', 'label' => 'Renseignez le code de confirmation reçu par mail'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data class' => ForgottenMdpController::class,
        ]);
    }
}
