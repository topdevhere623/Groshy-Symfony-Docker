<?php

declare(strict_types=1);

namespace Groshy\Tests\Functional\Api\Action;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Groshy\Entity\AssetType;
use Groshy\Entity\User;
use Groshy\Enum\Privacy;
use Talav\Component\Resource\Repository\RepositoryInterface;

class AssetInvestmentTest extends ApiTestCase
{
    private ?Generator $faker;

    private ?Client $client;

    private ?RepositoryInterface $assetInvestmentRepository;

    private ?User $testUser1;
    private ?User $testUser2;

    private const USER1 = 'user1';
    private const USER2 = 'user2';

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->client = static::createClient();

        $this->assetInvestmentRepository = $this->client->getContainer()->get('app.repository.asset_investment');
        $userManager = $this->client->getContainer()->get('app.manager.user');
        $this->testUser1 = $userManager->getRepository()->findOneBy(['username' => self::USER1]);
        $this->testUser2 = $userManager->getRepository()->findOneBy(['username' => self::USER2]);
        $this->client->getKernelBrowser()->loginUser($this->testUser2);
    }

    /**
     * @test
     */
    public function it_returns_404_for_private_asset_created_by_another_user(): void
    {
        $sponsor = $this->getRandomAssetInvestmentCreatedBy($this->testUser1);
        $this->client->request('GET', '/api/sponsors/'.$sponsor->getId()->__toString());
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @test
     */
    public function it_shows_error_when_asset_type_is_not_compatible_with_asset_class(): void
    {
        $this->client->request('POST', '/api/asset_investments', ['json' => [
            'name' => $this->faker->company,
            'privacy' => Privacy::PUBLIC,
            'assetType' => self::findIriBy(AssetType::class, ['name' => 'Cash']),
        ]]);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                0 => [
                    'propertyPath' => 'assetType',
                    'message' => 'This asset type is not compatible with provided asset or position',
                    'code' => null,
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_creates_new_asset_investment(): void
    {
        $response = $this->client->request('POST', '/api/asset_investments', ['json' => [
            'name' => $this->faker->company.' Investment fund '.$this->faker->numberBetween(1, 5),
            'privacy' => Privacy::PUBLIC,
            'assetType' => self::findIriBy(AssetType::class, ['name' => 'Real Estate LP Fund']),
            'website' => 'https://'.$this->faker->domainName(),
            'term' => '7-10',
            'irr' => '8-15',
            'multiple' => '2.0-2.2',
        ]]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/api/contexts/AssetInvestment',
            '@type' => 'AssetInvestment',
        ]);
        $this->assertMatchesRegularExpression('~^/api/asset_investments/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$~', $response->toArray()['@id']);
    }

    private function getRandomAssetInvestmentCreatedBy(User $user)
    {
        return $this->assetInvestmentRepository
            ->createQueryBuilder('ai')
            ->select('ai')
            ->andWhere('ai.createdBy = :user')
            ->andWhere('ai.privacy = :privacy')
            ->setParameter('user', $user)
            ->setParameter('privacy', Privacy::PRIVATE)
            ->orderBy('ai.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
