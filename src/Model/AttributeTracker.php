<?php

declare(strict_types=1);

namespace Groshy\Model;

final class AttributeTracker
{
    private array $valueGraph = [];

    private array $allocationGraph = [];

    private array $dashData = [];

    public function __construct(
        private readonly AttributeModel $trackable
    ) {
    }

    public function add(string $date, int $change): void
    {
        $currentValue = (0 == count($this->valueGraph)) ? 0 : end($this->valueGraph);
        $this->valueGraph[$date] = $currentValue + $change;
    }

    public function build(array $totalGraph): void
    {
        $prev = 0;
        foreach ($totalGraph as $date => $value) {
            $curValue = $this->valueGraph[$date] ?? $prev;
            $this->allocationGraph[$date] = $curValue / $value * 100;
            $prev = $curValue;
        }
        $this->dashData = [
            'trackable' => ['id' => $this->trackable->id, 'name' => $this->trackable->name],
            'value' => $this->getGraphData($this->valueGraph),
            'allocation' => $this->getGraphData($this->allocationGraph),
        ];
    }

    public function getValue(): array
    {
        return $this->getGraphData($this->valueGraph);
    }

    public function getData(): array
    {
        return $this->dashData;
    }

    public function getAllocation(): array
    {
        return $this->getGraphData($this->allocationGraph);
    }

    public function getTrackable(): object
    {
        return $this->trackable;
    }

    private function getGraphData(array $graph): array
    {
        if (0 == count($graph)) {
            return $this->getGraphEmptyResponse();
        }
        $first = reset($graph);
        $last = end($graph);

        return [
            'current' => $last,
            'graph' => $graph,
            'change' => [
                'amount' => $last - $first,
                'percent' => 0 == $last ? 0 : ($last - $first) / $last * 100,
            ],
        ];
    }

    private function getGraphEmptyResponse(): array
    {
        return [
            'current' => 0,
            'graph' => [],
            'change' => [
                'amount' => 0,
                'percent' => 0,
            ],
        ];
    }
}
