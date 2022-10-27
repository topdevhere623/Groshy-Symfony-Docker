<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Groshy\Faker\Provider\InvestmentValueProvider;
use Talav\Component\Resource\Manager\ManagerInterface;

abstract class BaseFixture extends Fixture
{
    protected Generator $faker;

    private array $referencesIndex = [];

    abstract protected function loadData(): void;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new InvestmentValueProvider($this->faker));
        $this->loadData();
    }

    protected function getRandomReference(string $className): object
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (0 === strpos($key, $className.'_')) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$className])) {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $className));
        }
        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);

        return $this->getReference($randomReferenceKey);
    }

    protected function addReferences(ManagerInterface $manager): void
    {
        foreach ($manager->getRepository()->findAll() as $entity) {
            $this->addReference($manager->getClassName().'_'.$entity->getId(), $entity);
        }
    }
}
