<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ZbiorkiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nazwa',TextType::class,['label'=>'name'])
        ->add('kwota_zbiorki',NumberType::class,['label'=>'collection.ammount'])
        ->add('start_zbiorki',DateTimeType::class,[
            'label'=>'collection.start_date'
        ])
        ->add('koniec_zbiorki',DateTimeType::class,['label'=>'collection.end_date'])
        ->add('status',CheckboxType::class, ['label'=>'collection.status'])

        ->add('save', SubmitType::class, array('label' => 'Zapisz'));
    }
}
