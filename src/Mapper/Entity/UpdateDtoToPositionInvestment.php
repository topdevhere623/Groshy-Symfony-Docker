<?php

declare(strict_types=1);

namespace Groshy\Mapper\Entity;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Groshy\Entity\PositionInvestment;
use Groshy\Message\Dto\PositionInvestment\UpdatePositionInvestmentDto;
use Webmozart\Assert\Assert;

class UpdateDtoToPositionInvestment extends CustomMapper
{
    public function mapToObject($source, $destination)
    {
        /* @var UpdatePositionInvestmentDto $source */
        Assert::isInstanceOf($source, UpdatePositionInvestmentDto::class);
        /* @var PositionInvestment $destination */
        Assert::isInstanceOf($destination, PositionInvestment::class);

        // https://stackoverflow.com/questions/30193351/how-to-update-doctrine-object-type-field
        $destination->setData(clone $destination->getData());
        if (!is_null($source->capitalCommitment)) {
            $destination->getData()->setCapitalCommitment($source->capitalCommitment);
        }
        if (!is_null($source->isDirect)) {
            $destination->getData()->setIsDirect($source->isDirect);
        }
        if (!is_null($source->institution)) {
            $destination->setInstitution($source->institution);
        }
        if (!is_null($source->notes)) {
            $destination->setNotes($source->notes);
        }
        if (!is_null($source->tags)) {
            $destination->setTags($source->tags);
        }

        return $destination;
    }
}
