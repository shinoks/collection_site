<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title',TextType::class,['label'=>'name'])
        ->add('text',TextareaType::class,['label'=>'treść'])
        ->add('isActive',CheckboxType::class,['label'=>'is_active'])

        ->add('save', SubmitType::class, array('label' => 'Zapisz'));
    }
}
