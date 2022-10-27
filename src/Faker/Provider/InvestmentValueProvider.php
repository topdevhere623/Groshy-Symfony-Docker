<?php

declare(strict_types=1);

namespace Groshy\Faker\Provider;

use Faker\Provider\Base;

class InvestmentValueProvider extends Base
{
    public function invested(string $type = 'regular'): int
    {
        return match ($type) {
            'small' => $this->generator->numberBetween(100, 20000) * 100,
            default => $this->generator->numberBetween(25, 100) * 1000 * 100,
        };
    }
}
