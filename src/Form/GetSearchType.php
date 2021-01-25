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
                'attr' => ['class' => 'titreLabel'],
                'help' => "Saisissez le genre de votre film ou serie"
            ])
            ->add('movie', CheckboxType::class, [
                'label'    => 'Film',
                'required' => false,
                'data' => true,
                'attr' => ['class' => 'titreLabel'],
            ])
            ->add('serie', CheckboxType::class, [
                'label'    => 'Série',
                'required' => false,
                'attr' => ['class' => 'titreLabel'],
            ])
            ->add('rating', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'class' => 'titreLabel',
                    'list' => 'tickmarks'
                ],
                'label' => 'Imdb',
                'required' => false,
                'help' => "Saisissez une rating valide entre 0,0 et 10,0",
            ])
            ->add('year', NumberType::class, [
                'label'    => 'Année',
                'required' => false,
                'attr' => [
                    'class' => 'titreLabel',
                    'min' => 1888,
                    'max' => 2021
                    ],
                'help' => "Saisissez une date valide entre 1888 et 2021"
            ])
            ->add('keyword', TextType::class, [
                'label'    => 'Mot-clé',
                'required' => false,
                'attr' => ['class' => 'titreLabel'],
                'help' => 'Example: Festival de cannes, Primé, Oscars, HBO, Hollywood, John Malkovich etc.'
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
