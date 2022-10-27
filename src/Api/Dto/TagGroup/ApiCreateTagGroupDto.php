<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\TagGroup;

//use Groshy\Validator\Constraints\UniqueDto;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ApiCreateTagGroupDto
{
    /**
     * @Groups({"post"})
     *
     * @Assert\NotBlank
     * @Assert\Length(max=250)
     */
    public ?string $name = null;

    /**
     * @Groups({"post"})
     *
     * @Assert\GreaterThanOrEqual(value=0)
     * @Assert\LessThanOrEqual(value=9999)
     */
    public ?int $position = null;
}
