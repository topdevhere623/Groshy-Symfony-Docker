<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler\Sponsor;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Entity\Sponsor;
use Groshy\Message\Command\Sponsor\CreateSponsorCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class CreateSponsorHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AutoMapperInterface $mapper,
        private readonly ManagerInterface $sponsorManager
    ) {
    }

    public function __invoke(CreateSponsorCommand $message): Sponsor
    {
        $sponsor = $this->mapper->mapToObject($message->dto, $this->sponsorManager->create());
        $this->sponsorManager->update($sponsor);

        return $sponsor;
    }
}
