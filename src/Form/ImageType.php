<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,['label'=>'Nome da imagem'])
            ->add('path', FileType::class,
                  [
                      'label' => 'Imagem do produto',
                      'constraints' => [
                        new File([
                            'maxSize' => '5000k',
                            'mimeTypes' => [
                                'image/gif',
                                'image/png',
                                'image/jpeg',
                            ],
                            'mimeTypesMessage' => 'Por favor selecione uma imagem valida',
                        ])
                    ],
                    'data_class' => null,
                  ]
            )
            ->add('title', TextType::class, ['label'=> 'Titulo da imagem'])
            ->add('product',EntityType::class,[
                'data_class' => null,
                'class' => Product::class,
                'choice_label' => function ($produtc) {
                    return $produtc->getTitle();
                },
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
