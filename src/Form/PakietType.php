<?php
namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Zbiorki;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class PakietType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nazwa',TextType::class,['label'=>'name'])
        ->add('zawartosc',TextareaType::class,['label'=>'text'])
        ->add('ilosc_akcji',NumberType::class,['label'=>'collection.number_of_shares'])
        ->add('status',CheckboxType::class, ['label'=>'collection.status'])
        ->add('zbiorka', EntityType::class, array(
            'label'=>'zbiorka',
            'class' => Zbiorki::class,
            'choice_label' => 'nazwa',
        ))
        ->add('image', FileType::class, array(
            'label' => 'main_image',
            'required' => false,
            'data_class' => null
        ))

        ->add('save', SubmitType::class, array('label' => 'Zapisz'));
    }
}
