<?php

declare(strict_types=1);

namespace Groshy\Api\Action\Asset;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Api\Dto\AssetInvestment\ApiCreateAssetInvestmentDto;
use Groshy\Entity\AssetInvestment;
use Groshy\Message\Command\AssetInvestment\CreateAssetInvestmentCommand;
use Groshy\Message\Dto\AssetInvestment\CreateAssetInvestmentDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Core\Security;

class CreateAssetInvestmentAction
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly AutoMapperInterface $mapper,
        private readonly Security $security,
    ) {
    }

    public function __invoke(ApiCreateAssetInvestmentDto $data): AssetInvestment
    {
        $dto = $this->mapper->map($data, CreateAssetInvestmentDto::class);
        $dto->createdBy = $this->security->getUser();
        $envelope = $this->bus->dispatch(new CreateAssetInvestmentCommand($dto));
        // get the value that was returned by the last message handler
        return $envelope->last(HandledStamp::class)->getResult();
    }
}
