<?php

namespace App\Form;

use App\TMDB\TMDB;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GetSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'required' => false,
                'choice_loader' => new CallbackChoiceLoader(function() {
                    return (new TMDB())->getCategories();
                }),
            ])
            ->add('movie', CheckboxType::class, [
                'label'    => 'Film',
                'required' => false,
                'data' => true,
            ])
            ->add('serie', CheckboxType::class, [
                'label'    => 'Série',
                'required' => false,
            ])
            ->add('rating', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'step' => 1
                ],
                'label' => 'Imdb',
                'required' => false,
            ])
            ->add('year', NumberType::class, [
                'label'    => 'Année',
                'required' => false,
            ])
            ->add('keyword', TextType::class, [
                'label'    => 'Mot-clé',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
