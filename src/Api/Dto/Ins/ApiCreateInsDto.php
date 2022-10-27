<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\Ins;

use Symfony\Component\Validator\Constraints as Assert;

class ApiCreateInsDto
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max=250)
     */
    public ?string $name = null;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=250)
     * @Assert\Url
     */
    public ?string $website = null;
}
