<?php

namespace Groshy\Mapper\Config;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Groshy\Api\Dto\AssetInvestment\ApiCreateAssetInvestmentDto;
use Groshy\Api\Dto\PositionInvestment\ApiCreatePositionInvestmentDto;
use Groshy\Api\Dto\PositionInvestment\ApiUpdatePositionInvestmentDto;
use Groshy\Api\Dto\Transaction\ApiCreateTransactionDto;
use Groshy\Entity\AssetInvestment;
use Groshy\Entity\AssetInvestmentData;
use Groshy\Entity\PositionInvestment;
use Groshy\Enum\Privacy;
use Groshy\Enum\TransactionType;
use Groshy\Mapper\Entity\CreateDtoToPositionInvestment;
use Groshy\Mapper\Entity\UpdateDtoToPositionInvestment;
use Groshy\Message\Dto\AssetInvestment\CreateAssetInvestmentDto;
use Groshy\Message\Dto\PositionInvestment\CreatePositionInvestmentDto;
use Groshy\Message\Dto\PositionInvestment\UpdatePositionInvestmentDto;
use Groshy\Message\Dto\Transaction\CreateTransactionDto;

class MapperConfig implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(ApiCreateAssetInvestmentDto::class, CreateAssetInvestmentDto::class)
            ->forMember('privacy', function (ApiCreateAssetInvestmentDto $api) {
                return Privacy::from($api->privacy);
            });
        $config->registerMapping(CreateAssetInvestmentDto::class, AssetInvestment::class)
            ->forMember('data', function (CreateAssetInvestmentDto $dto, AutoMapperInterface $mapper, array $context) {
                /** @var AssetInvestmentData $currentData */
                $currentData = $context[AutoMapper::DESTINATION_CONTEXT]->getData();
                $currentData->setWebsite($dto->website);
                $currentData->setIrr($dto->irr);
                $currentData->setIsEvergreen($dto->isEvergreen);
                $currentData->setMultiple($dto->multiple);
                $currentData->setTerm($dto->term);

                return $currentData;
            });
        $this->configureForPositionInvestment($config);
        $this->configureForTransaction($config);
    }

    public function configureForPositionInvestment(AutoMapperConfigInterface $config)
    {
        $config->registerMapping(ApiCreatePositionInvestmentDto::class, CreatePositionInvestmentDto::class)
            ->forMember('capitalCommitment', function (ApiCreatePositionInvestmentDto $api) {
                return intval($api->capitalCommitment * 100);
            });
        $config->registerMapping(ApiUpdatePositionInvestmentDto::class, UpdatePositionInvestmentDto::class)
            ->forMember('capitalCommitment', function (ApiUpdatePositionInvestmentDto $api) {
                return intval($api->capitalCommitment * 100);
            });

        $config->registerMapping(CreatePositionInvestmentDto::class, PositionInvestment::class)
            ->useCustomMapper(new CreateDtoToPositionInvestment());
        $config->registerMapping(UpdatePositionInvestmentDto::class, PositionInvestment::class)
            ->useCustomMapper(new UpdateDtoToPositionInvestment());
    }

    public function configureForTransaction(AutoMapperConfigInterface $config)
    {
        $config->registerMapping(ApiCreateTransactionDto::class, CreateTransactionDto::class)
            ->forMember('type', function (ApiCreateTransactionDto $api) {
                return TransactionType::from($api->type);
            })
            ->forMember('value', function (ApiCreateTransactionDto $api) {
                return intval($api->value * 100);
            });
    }
}
