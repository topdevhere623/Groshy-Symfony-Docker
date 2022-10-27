<?php

declare(strict_types=1);

namespace Groshy\Message\Dto\Ins;

use Talav\Component\User\Model\UserInterface;

class CreateInsDto
{
    public ?string $name = null;

    public ?string $website = null;

    public ?UserInterface $createdBy = null;
}
