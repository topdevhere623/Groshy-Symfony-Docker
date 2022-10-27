<?php

declare(strict_types=1);

namespace Groshy\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'talav.form.email', 'translation_domain' => 'TalavUserBundle'])
            ->add('username', null, ['label' => 'talav.form.username', 'translation_domain' => 'TalavUserBundle'])
            ->add('firstName', null, ['label' => 'First Name'])
            ->add('lastName', null, ['label' => 'Last Name'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'groshy_user_profile';
    }
}
