<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\Sponsor;

use Groshy\Enum\Privacy;
use Symfony\Component\Validator\Constraints as Assert;

class ApiCreateSponsorDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    #[Assert\Url]
    public ?string $website = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Privacy::class, 'choices'])]
    public ?string $privacy;
}
