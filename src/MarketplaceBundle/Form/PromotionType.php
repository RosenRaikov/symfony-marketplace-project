<?php

namespace MarketplaceBundle\Form;

use MarketplaceBundle\Entity\Category;
use MarketplaceBundle\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('percent', NumberType::class, ['label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'label_attr' => [
                    'class' => 'control-label'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('endDate', DateType::class, ['widget' => 'single_text',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control'
                ],
                'placeholder' => 'Select category',
                'required' => false
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'marketplace_bundle_promotion_type';
    }
}
