<?php

declare(strict_types=1);

namespace Groshy\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ProfileFormModel
{
    #[Assert\NotBlank(message: 'talav.email.blank')]
    #[Assert\Email(message: 'talav.email.invalid', mode: 'strict')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'talav.username.blank')]
    public ?string $username = null;

    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    public ?string $lastName = null;
}
