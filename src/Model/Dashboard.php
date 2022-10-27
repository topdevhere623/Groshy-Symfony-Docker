<?php

declare(strict_types=1);

namespace Groshy\Model;

use DateTime;
use Groshy\Entity\Position;
use Groshy\Entity\PositionInvestment;
use Groshy\Entity\PositionValue;

final class Dashboard
{
    public const POSITION = 'position';

    public const DATE_FORMAT = 'Y-m-d';

    private array $collection = [];

    private ?DateTime $from = null;

    private array $dashData = [];

    private array $positionCache = [];

    public static function toDashData(array $initial, array $values, DateTime $from): array
    {
        $dash = new Dashboard();
        $dash->addInitialValues($initial, $from);
        $dash->addValues($values);
        $dash->buildDash();

        return $dash->getDashData();
    }

    public function addValues(array $values): void
    {
        foreach ($values as $value) {
            $this->addValue($value);
        }
    }

    public function addValue(PositionValue $value): void
    {
        $change = $this->calculateChange($value);
        $this->positionCache[$value->getPosition()->getId()->toString()] = $value->getPosition();
        foreach ($this->extractAttributes($value->getPosition()) as $type => $model) {
            $this->getAttributeTracker($type, $model)->add($value->getValueDate()->format(self::DATE_FORMAT), $change);
        }
    }

    public function addInitialValues(array $values, DateTime $from): void
    {
        $this->from = $from;
        foreach ($values as $value) {
            $this->addInitialValue($value, $from);
        }
    }

    public function addInitialValue(PositionValue $value, DateTime $from): void
    {
        $this->positionCache[$value->getPosition()->getId()->toString()] = $value->getPosition();
        foreach ($this->extractAttributes($value->getPosition()) as $type => $model) {
            $this->getAttributeTracker($type, $model)->add($from->format(self::DATE_FORMAT), $value->getValue());
        }
    }

    public function buildDash(): void
    {
        if (0 == count($this->collection)) {
            return;
        }
        // Initialize asset trackers for blance and liabilities
        $this->getAttributeTracker('balance', new AttributeModel('liability', 'liability'));
        $this->getAttributeTracker('balance', new AttributeModel('asset', 'asset'));
        $totalGraph = reset($this->collection['user'])->getValue()['graph'];
        foreach ($this->collection as $trackers) {
            foreach ($trackers as $tracker) {
                $tracker->build($totalGraph);
            }
        }

        $this->dashData = [
            'total' => reset($this->collection['user'])->getData(),
            'assets' => $this->getAttributeTracker('balance', new AttributeModel('asset', 'asset'))->getData(),
            'liabilities' => $this->getAttributeTracker('balance', new AttributeModel('liability', 'liability'))->getData(),
            'root_types' => array_map(function ($el) {
                return $el->getData();
            }, $this->collection['root_type']),
            'types' => array_map(function ($el) {
                return $el->getData();
            }, $this->collection['type']),
            'positions' => array_map(function ($el) {
                return $el->getData();
            }, $this->collection['position']),
            'position_total' => $this->getPositionTotal(),
        ];
    }

    public function getDashData(): array
    {
        return $this->dashData;
    }

    private function getPositionTotal(): array
    {
        $return = [
            'count_total' => 0,
            'count_active' => 0,
            'count_completed' => 0,
            'count_new' => 0,
            'capital_committed' => 0,
            'capital_called' => 0,
            'capital_called_percent' => 0,
            'distribution' => 0,
            'unrealized_multiple' => 0,
        ];
        /** @var AttributeTracker $tracker */
        foreach ($this->collection[self::POSITION] as $tracker) {
            /** @var Position $position */
            $position = $this->positionCache[$tracker->getTrackable()->id];
            ++$return['count_total'];
            $position->isCompleted() ? $return['count_completed']++ : $return['count_active']++;
            $position->getStartDate() >= $this->from ? $return['count_new']++ : null;
            if ($position instanceof PositionInvestment) {
                $return['capital_committed'] += $position->getData()->getCapitalCommitment();
                $return['capital_called'] += $position->getData()->getCapitalCalled();
                $return['distribution'] += $position->getGeneratedIncome();
            }
        }
        $return['capital_called_percent'] = $return['capital_committed'] > 0 ? $return['capital_called'] / $return['capital_committed'] * 100 : 0;
        $return['unrealized_multiple'] = $return['capital_called'] > 0 ? (reset($this->collection['user'])->getData()['value']['current'] + $return['distribution']) / $return['capital_called'] : 0;

        return $return;
    }

    private function extractAttributes(Position $position): array
    {
        $type = $position->getAsset()->getAssetType();

        return [
            'position' => AttributeModel::fromPosition($position),
            'type' => AttributeModel::fromType($type),
            'root_type' => $type->isTopLevel() ? AttributeModel::fromType($type) : AttributeModel::fromType($type->getParent()),
            'user' => AttributeModel::fromUser($position->getCreatedBy()),
            'balance' => $type->isAsset() ? new AttributeModel('asset', 'asset') : new AttributeModel('liability', 'liability'),
        ];
    }

    private function getAttributeTracker(string $type, AttributeModel $model): AttributeTracker
    {
        if (!isset($this->collection[$type])) {
            $this->collection[$type] = [];
        }
        if (!isset($this->collection[$type][$model->id])) {
            $this->collection[$type][$model->id] = new AttributeTracker($model);
        }

        return $this->collection[$type][$model->id];
    }

    private function calculateChange(PositionValue $value): int
    {
        return $value->getValue() - $this->getAttributeTracker(self::POSITION, AttributeModel::fromPosition($value->getPosition()))->getValue()['current'];
    }
}
