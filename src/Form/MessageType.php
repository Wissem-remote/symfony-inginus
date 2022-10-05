<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', ChoiceType::class, [
            'choices'  => [
                'Ou en est la livraisson ?' => 'Livraison',
                'Demande de Reboursement' => 'reboursement',
                'Produit non conforme' => 'hs',
                'Produit ne fonctionne pas !' => 'etat',
                'Autre Demande' => 'Autre'
            ],
        ])
            ->add('message', TextareaType::class)
            // ->add('state')
            // ->add('createdAt')
            // ->add('user')
            // ->add('orders')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
