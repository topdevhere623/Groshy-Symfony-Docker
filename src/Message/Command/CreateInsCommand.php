<?php

declare(strict_types=1);

namespace Groshy\Message\Command;

use Groshy\Message\Dto\Ins\CreateInsDto;
use Talav\Component\Resource\Model\DomainEventInterface;

final class CreateInsCommand implements DomainEventInterface
{
    public function __construct(
        public CreateInsDto $dto
    ) {
    }
}
