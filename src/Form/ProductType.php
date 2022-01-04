<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($options['add']==true):

        $builder
            ->add('title',TextType::class)
            ->add('price', NumberType::class)
            ->add('picture', FileType::class)
            ->add('Enregistrer', SubmitType::class)
        ;

        elseif ($options['update']==true):
        $builder
            ->add('title',TextType::class)
            ->add('price', NumberType::class)
            ->add('pictureUpdate', FileType::class)
            ->add('Enregistrer', SubmitType::class)
        ;
        endif;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'add'=>false,
            'update'=>false
        ]);
    }
}
