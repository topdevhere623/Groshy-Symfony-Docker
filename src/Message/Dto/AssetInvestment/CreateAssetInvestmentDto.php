<?php

declare(strict_types=1);

namespace Groshy\Message\Dto\AssetInvestment;

use Groshy\Entity\AssetType;
use Groshy\Entity\Sponsor;
use Groshy\Enum\Privacy;
use Talav\Component\User\Model\UserInterface;

class CreateAssetInvestmentDto
{
    public ?string $name = null;

    public Privacy $privacy = Privacy::PRIVATE;

    public ?Sponsor $sponsor = null;

    public ?AssetType $assetType = null;

    public ?UserInterface $createdBy = null;

    public ?string $website;

    public bool $isEvergreen = false;

    public ?string $term;

    public ?string $irr;

    public ?string $multiple;
}
