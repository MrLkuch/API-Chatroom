<?php

namespace App\Form;

use App\Entity\Chatroom;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('sentAt', null, [
                'widget' => 'single_text',
            ])
            ->add('_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('chatroom', EntityType::class, [
                'class' => Chatroom::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
