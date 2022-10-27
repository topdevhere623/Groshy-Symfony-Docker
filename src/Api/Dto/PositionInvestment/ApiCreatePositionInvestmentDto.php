<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\PositionInvestment;

use Groshy\Entity\AssetInvestment;
use Groshy\Entity\Institution;
use Groshy\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;

class ApiCreatePositionInvestmentDto
{
    #[Assert\NotBlank]
    #[Assert\GreaterThan(1)]
    public float $capitalCommitment = 0;

    public bool $isDirect = false;

    public ?Institution $institution = null;

    #[Assert\NotBlank]
    public ?AssetInvestment $asset = null;

    public ?string $notes = null;

    /**
     * @var array<Tag>
     */
    public array $tags = [];
}
