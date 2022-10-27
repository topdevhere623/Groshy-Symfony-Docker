<?php

declare(strict_types=1);

namespace Groshy\Message\Dto\PositionInvestment;

use Groshy\Entity\Institution;

class UpdatePositionInvestmentDto
{
    public ?int $capitalCommitment = null;

    public ?bool $isDirect = null;

    public ?Institution $institution = null;

    public ?string $notes = null;

    public ?array $tags = null;
}
