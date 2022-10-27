<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Entity\PositionInvestment;
use Talav\Component\Resource\Manager\ManagerInterface;

final class TagFixtures extends BaseFixture implements OrderedFixtureInterface
{
    private array $tagCache = [];

    public function __construct(
        private readonly ManagerInterface $positionInvestmentManager,
        private readonly ManagerInterface $tagManager,
    ) {
    }

    public function loadData(): void
    {
        $this->loadTags();
        $invs = $this->positionInvestmentManager->getRepository()->findAll();
        /** @var PositionInvestment $inv */
        foreach ($invs as $inv) {
            if ($this->faker->numberBetween(0, 100) > 30) {
                continue;
            }
            $temp = $this->faker->numberBetween(0, 100);
            $tagsCount = $temp < 70 ? 1 : ($temp < 90 ? 2 : 3);
            for ($i = 1; $i <= $tagsCount; ++$i) {
                $userId = strval($inv->getCreatedBy()->getId());
                $tags = $this->faker->randomElements($this->tagCache[$userId], $tagsCount);
                $inv->addTags($tags);
            }
        }

        $this->positionInvestmentManager->flush();
    }

    public function getOrder(): int
    {
        return 100;
    }

    private function loadTags(): void
    {
        foreach ($this->tagManager->getRepository()->findAll() as $tag) {
            $userId = strval($tag->getCreatedBy()->getId());
            if (!isset($this->tagCache[$userId])) {
                $this->tagCache[$userId] = [];
            }
            $this->tagCache[$userId][] = $tag;
        }
    }
}
