<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Groshy\Entity\Institution;
use Talav\Component\Resource\Manager\ManagerInterface;

final class InstitutionFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly ManagerInterface $institutionManager,
    ) {
    }

    public function loadData(): void
    {
        if (($handle = fopen(dirname(__FILE__).'/files/ins.csv', 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                /** @var Institution $ins */
                $ins = $this->institutionManager->create();
                $ins->setName(trim($data[0]));
                $ins->setWebsite(trim($data[1]));
                $this->institutionManager->update($ins);
            }
        }
        $this->institutionManager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
