<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Tag; // Обязательно импортируем сущность Tag
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextType::class, [
                'label' => 'Текст заметки',
                'attr' => ['placeholder' => 'Напишите текст заметки тут'],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,     // Исправлено: передаем класс через ::class
                'choice_label' => 'name',  // Исправлена опечатка (choice_label вместо choise_label)
                'multiple' => true,        // Исправлено: булево значение вместо строки
                'expanded' => true,        // Исправлено: булево значение вместо строки
                'required' => false,       // Исправлено: булево значение вместо строки
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'class' => 'btn btn-primary w-100',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}