<?php

namespace App\Form;

use App\Entity\Skills;
use App\Entity\Worker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class WorkerType extends AbstractType
{
    


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('visibility', CheckboxType::class, [
            'label' => 'Visibilité',
            'required' => false,
            ])      
            ->add('firstname', TextType::class,[
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Nom de famille',
            ])
            ->add('age', DateType::class, [                
                'label' => 'Date de naissance',
                'years' => $options['years_range']
            ])
            ->add('gender', TextType::class,[
                'label' => 'Genre',
            ])
            ->add('description', TextType::class)
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'choice_label' => 'name',
                'label' => 'Compétences',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
                  
            ->add('cv', FileType::class, [
                'label' => 'CV (PDF file)',
                'mapped' =>false,
                'required' =>false,
                'constraints' =>[
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF  document',
                    ])
                ],
                'mapped' =>false,
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $currentYear = (int)date('Y');
        $yearsRange = range($currentYear - 100, $currentYear);

        $resolver->setDefaults([
            'years_range' => $yearsRange,
            'data_class' => Worker::class,
        ]);
    }
}
