<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('size', ChoiceType::class, [
                'choices' => [
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                ],
                'label' => 'Taille',
            ])
            ->add("stock")
            ->add("image", FileType::class,[
                'label' => 'image de produit',
                'mapped'=>false,
                'required'=>false,
                "constraints"=>[
                    new File([
                        "maxSize"=>"1024k",
                        "mimeTypes"=>[
                            'image/jpg',
                            'image/png',
                            'image/jpeg',
                        ],
                        'maxSizeMessage'=>'votre image ne doit pas depasser les 1024ko',
                        'mimeTypesMessage'=>"votre image de couverture doit etre au format valide(png, jpg, jpeg)"
                    ])
                ]
            ])
            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
