<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler\AssetInvestment;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Entity\AssetInvestment;
use Groshy\Message\Command\AssetInvestment\CreateAssetInvestmentCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class CreateAssetInvestmentHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AutoMapperInterface $mapper,
        private readonly ManagerInterface $assetInvestmentManager
    ) {
    }

    public function __invoke(CreateAssetInvestmentCommand $message): AssetInvestment
    {
        $asset = $this->mapper->mapToObject($message->dto, $this->assetInvestmentManager->create());
        $this->assetInvestmentManager->update($asset);

        return $asset;
    }
}
