<?php

declare(strict_types=1);

namespace Groshy\Tests\Functional\Api\Action;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Groshy\Entity\Position;
use Groshy\Entity\Transaction;
use Groshy\Entity\User;
use Groshy\Enum\TransactionType;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\Resource\Repository\RepositoryInterface;

class TransactionTest extends ApiTestCase
{
    private ?Generator $faker;

    private ?Client $client;

    private ?ManagerInterface $transactionManager;
    private ?ManagerInterface $userManager;
    private ?RepositoryInterface $positionRepository;

    private ?User $testUser1;
    private ?User $testUser2;

    private const USER1 = 'user1';
    private const USER2 = 'user2';
    private const USER3 = 'user3';
    private const USER4 = 'user4';
    private const USER5 = 'user5';

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->client = static::createClient();

        $this->transactionManager = $this->client->getContainer()->get('app.manager.transaction');
        $this->positionRepository = $this->client->getContainer()->get('app.repository.position');

        $this->userManager = $this->client->getContainer()->get('app.manager.user');
        $this->testUser1 = $this->userManager->getRepository()->findOneBy(['username' => self::USER1]);
        $this->testUser2 = $this->userManager->getRepository()->findOneBy(['username' => self::USER2]);
        $this->client->getKernelBrowser()->loginUser($this->testUser2);
    }

    /**
     * @test
     */
    public function it_returns_404_for_transactions_for_positions_created_by_another_user(): void
    {
        foreach ([self::USER1, self::USER3, self::USER4, self::USER5] as  $userName) {
            $user = $this->userManager->getRepository()->findOneBy(['username' => $userName]);
            foreach ($this->getRandomTransactionCreatedBy($user, 2) as $transaction) {
                $this->client->request('GET', '/api/transactions/'.$transaction->getId());
                $this->assertResponseStatusCodeSame(404);
            }
        }
    }

    /**
     * @test
     */
    public function it_only_gets_transactions_for_positions_for_current_user(): void
    {
        $response = $this->client->request('GET', '/api/transactions');
        foreach ($response->toArray()['hydra:member'] as $trans) {
            self::assertStringContainsString($this->testUser2->getId()->__toString(), $trans['position']['createdBy']);
        }
    }

    /**
     * @test
     */
    public function it_creates_new_transaction(): void
    {
        $positions = $this->positionRepository->findBy(['createdBy' => $this->testUser2]);
        $position = $this->faker->randomElement($positions);
        $response = $this->client->request('POST', '/api/transactions', ['json' => [
            'valueDate' => '2022-06-13T02:26:41.846Z',
            'value' => 1000.00,
            'type' => TransactionType::DISTRIBUTION->value,
            'position' => static::findIriBy(Position::class, ['id' => $position->getId()]),
            'isReinvested' => true,
        ]]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertMatchesRegularExpression('~^/api/transactions/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$~', $response->toArray()['@id']);
        /** @var Transaction $transaction */
        $transaction = $this->transactionManager->getRepository()->find($response->toArray()['id']);
        self::assertEquals(100000, $transaction->getValue());
        self::assertEquals($position, $transaction->getPosition());
        self::assertEquals(TransactionType::DISTRIBUTION, $transaction->getType());
    }

    private function getRandomTransactionCreatedBy(User $user, int $counter = 3): array
    {
        $transactions = $this->transactionManager->getRepository()->byUser($user);

        return $this->faker->randomElements($transactions, $counter);
    }
}
