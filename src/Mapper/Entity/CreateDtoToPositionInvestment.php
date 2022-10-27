<?php

declare(strict_types=1);

namespace Groshy\Mapper\Entity;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Groshy\Entity\PositionInvestment;
use Groshy\Message\Dto\PositionInvestment\CreatePositionInvestmentDto;
use Webmozart\Assert\Assert;

class CreateDtoToPositionInvestment extends CustomMapper
{
    public function mapToObject($source, $destination)
    {
        /* @var CreatePositionInvestmentDto $source */
        Assert::isInstanceOf($source, CreatePositionInvestmentDto::class);
        /* @var PositionInvestment $destination */
        Assert::isInstanceOf($destination, PositionInvestment::class);

        $destination->getData()->setCapitalCommitment($source->capitalCommitment);
        $destination->getData()->setIsDirect($source->isDirect);
        $destination->setInstitution($source->institution);
        $destination->setAsset($source->asset);
        $destination->setCreatedBy($source->createdBy);
        $destination->setNotes($source->notes);
        $destination->setTags($source->tags);

        return $destination;
    }
}
