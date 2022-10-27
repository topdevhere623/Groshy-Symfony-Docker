<?php

declare(strict_types=1);

namespace Groshy\Api\Dto\PositionInvestment;

use Groshy\Entity\Institution;
use Groshy\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;

class ApiUpdatePositionInvestmentDto
{
    #[Assert\GreaterThan(1)]
    public ?float $capitalCommitment = null;

    public ?bool $isDirect = null;

    public ?Institution $institution = null;

    public ?string $notes = null;

    /**
     * @var array<Tag>
     */
    public ?array $tags = null;
}
