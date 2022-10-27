<?php

declare(strict_types=1);

namespace Groshy\Provider;

use Groshy\Enum\Color;

final class DefaultTagProvider
{
    public function getTagsStructure(): array
    {
        return [
            [
                'name' => 'Risk Tolerance',
                'position' => 0,
                'tags' => [
                    [
                        'name' => 'Conservative',
                        'position' => 0,
                        'color' => Color::PINK,
                    ],
                    [
                        'name' => 'Moderate',
                        'position' => 1,
                        'color' => Color::PINK,
                    ],
                    [
                        'name' => 'Aggressive',
                        'position' => 2,
                        'color' => Color::ORANGE,
                    ],
                ],
            ],
            [
                'name' => 'Investment Horizon',
                'position' => 1,
                'tags' => [
                    [
                        'name' => 'Short-term',
                        'position' => 0,
                        'color' => Color::PINK,
                    ],
                    [
                        'name' => 'Long-term',
                        'position' => 1,
                        'color' => Color::ORANGE,
                    ],
                ],
            ],
        ];
    }
}
