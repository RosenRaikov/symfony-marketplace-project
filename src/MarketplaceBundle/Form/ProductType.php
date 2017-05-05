<?php

namespace MarketplaceBundle\Form;

use MarketplaceBundle\Entity\Category;
use MarketplaceBundle\Entity\Product;
use MarketplaceBundle\Repository\CategoryRepository;
use function Sodium\add;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('price', MoneyType::class, ['currency' => 'USD'])
            ->add('description', TextareaType::class)
            ->add('category')
            ->add('stock', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Product::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'marketplace_bundle_product_type';
    }
}
