<?php

declare(strict_types=1);

namespace Groshy\Tests\Functional\Api\Action;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Groshy\Entity\AssetInvestment;
use Groshy\Entity\Institution;
use Groshy\Entity\PositionInvestment;
use Groshy\Entity\Tag;
use Groshy\Entity\User;
use Talav\Component\Resource\Manager\ManagerInterface;

class PositionInvestmentTest extends ApiTestCase
{
    private ?Generator $faker;

    private ?Client $client;

    private ?ManagerInterface $positionInvestmentManager;

    private ?ManagerInterface $userManager;

    private ?ManagerInterface $institutionManager;

    private ?ManagerInterface $tagManager;

    private ?User $testUser2;
    private ?User $testUser1;

    private const USER1 = 'user1';
    private const USER2 = 'user2';
    private const ASSET = 'Christina 5 Wealth Builder Fund';

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->client = static::createClient();
        $this->positionInvestmentManager = $this->client->getContainer()->get('app.manager.position_investment');
        $this->userManager = $this->client->getContainer()->get('app.manager.user');
        $this->institutionManager = $this->client->getContainer()->get('app.manager.institution');
        $this->tagManager = $this->client->getContainer()->get('app.manager.tag');
        $this->testUser2 = $this->userManager->getRepository()->findOneBy(['username' => self::USER2]);
        $this->testUser1 = $this->userManager->getRepository()->findOneBy(['username' => self::USER1]);
        $this->client->getKernelBrowser()->loginUser($this->testUser2);
    }

    /**
     * @test
     */
    public function it_reads_position_investment_by_id(): void
    {
        // user2 is a logged in user
        $investment = $this->getRandomPositionInvestment($this->testUser2);

        $this->client->request('GET', '/api/position_investments/'.$investment->getId()->toString());
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @test
     */
    public function it_returns_404_for_investments_created_by_another_user(): void
    {
        $investment = $this->getRandomPositionInvestment($this->testUser1);
        $this->client->request('GET', '/api/position_investments/'.$investment->getId()->__toString());
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @test
     */
    public function it_creates_new_position_investment(): void
    {
        $isDirect = $this->faker->boolean;
        $response = $this->client->request('POST', '/api/position_investments', ['json' => [
            'capitalCommitment' => $this->faker->numberBetween(25, 50) * 1000,
            'isDirect' => $isDirect,
            'institution' => $isDirect ? null : static::findIriBy(Institution::class, ['id' => $this->getRandomInstitution()->getId()]),
            'asset' => static::findIriBy(AssetInvestment::class, [
                'name' => self::ASSET,
            ]),
            'notes' => $this->faker->boolean ? $this->faker->text(200) : null,
            'tags' => $this->faker->boolean ?
                array_map(function ($el) {return static::findIriBy(Tag::class, ['id' => $el->getId()]); }, $this->getRandomTags($this->testUser2, $this->faker->numberBetween(1, 3))) : [],
        ]]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/api/contexts/PositionInvestment',
            '@type' => 'PositionInvestment',
        ]);
        $this->assertMatchesRegularExpression('~^/api/position_investments/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$~', $response->toArray()['@id']);
    }

    /**
     * @test
     */
    public function it_updates_position_investment(): void
    {
        $investment = $this->getRandomPositionInvestment($this->testUser2);
        $newCapital = $investment->getData()->getCapitalCommitment() + 2000;
        $newDirect = !$investment->getData()->isDirect();
        $this->client->request('PATCH', '/api/position_investments/'.$investment->getId(), [
            'json' => [
                'capitalCommitment' => $newCapital,
                'isDirect' => $newDirect,
            ],
            'headers' => ['Content-Type' => 'application/merge-patch+json'], ]);
        $this->assertResponseStatusCodeSame(200);
        $this->positionInvestmentManager->reload($investment);
        self::assertEquals($newCapital * 100, $investment->getData()->getCapitalCommitment());
        self::assertEquals($newDirect, $investment->getData()->isDirect());
    }

    /**
     * @test
     */
    public function it_deletes_position_investment(): void
    {
        $investment = $this->getRandomPositionInvestment($this->testUser2);
        $id = $investment->getId();
        $this->client->request('DELETE', '/api/position_investments/'.$investment->getId());
        $this->assertResponseStatusCodeSame(204);
        self::assertNull($this->positionInvestmentManager->getRepository()->find($id));
    }

    private function getRandomPositionInvestment(User $user): PositionInvestment
    {
        $positions = $this->positionInvestmentManager->getRepository()->findBy(['createdBy' => $user]);

        return $positions[array_rand($positions)];
    }

    private function getRandomInstitution(): Institution
    {
        $institutions = $this->institutionManager->getRepository()->findAll();

        return $institutions[array_rand($institutions)];
    }

    private function getRandomTags(User $user, int $count = 1): array
    {
        $tags = $this->tagManager->getRepository()->findBy(['createdBy' => $user]);

        return $this->faker->randomElements($tags, $count);
    }
}
