<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\AssetInvestment;

use Groshy\Entity\AssetInvestment;
use Groshy\Entity\AssetType;
use Groshy\Entity\Sponsor;
use Groshy\Enum\Privacy;
use Groshy\Validator\Constraints as GroshyAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ApiCreateAssetInvestmentDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Privacy::class, 'choices'])]
    public ?string $privacy;

    public ?Sponsor $sponsor = null;

    #[Assert\NotBlank]
    #[GroshyAssert\AssetTypeMatch(assetClass: AssetInvestment::class)]
    public ?AssetType $assetType = null;

    #[Assert\Url]
    public ?string $website;

    public bool $isEvergreen = false;

    public ?string $term;

    public ?string $irr;

    public ?string $multiple;
}
