<?php

declare(strict_types=1);

namespace Groshy\Api\Action\Sponsor;

use Groshy\Api\Dto\Sponsor\ApiCreateSponsorDto;
use Groshy\Entity\Sponsor;
use Groshy\Enum\Privacy;
use Groshy\Message\Command\Sponsor\CreateSponsorCommand;
use Groshy\Message\Dto\Sponsor\CreateSponsorDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Security;

class CreateSponsorAction
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly Security $security,
    ) {
    }

    public function __invoke(ApiCreateSponsorDto $data): Sponsor
    {
        $commandDto = new CreateSponsorDto();
        $commandDto->name = $data->name;
        $commandDto->website = $data->website;
        $commandDto->privacy = Privacy::from($data->privacy);
        $commandDto->createdBy = $this->security->getUser();

        $envelope = $this->bus->dispatch(new CreateSponsorCommand($commandDto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
