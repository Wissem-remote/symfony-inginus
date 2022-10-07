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
                'Ou en est la livraisson ?' => 'Ou en est la livraisson ?',
                'Demande de Reboursement' => 'Demande de Reboursement',
                'Produit non conforme' => 'Produit non conforme',
                'Produit ne fonctionne pas !' => 'Produit ne fonctionne pas !',
                'Autre Demande' => 'Autre Demande'
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
        $resolver->setDefaults([]);
    }
}
