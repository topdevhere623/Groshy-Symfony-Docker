<?php

declare(strict_types=1);

namespace Groshy\Tests\Functional\Api\Action;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Groshy\Entity\User;
use Groshy\Enum\Privacy;
use Talav\Component\Resource\Repository\RepositoryInterface;

class SponsorTest extends ApiTestCase
{
    private ?Generator $faker;

    private ?Client $client;

    private ?RepositoryInterface $sponsorRepository;

    private ?User $testUser1;
    private ?User $testUser2;

    private const USER2 = 'user2';
    private const USER1 = 'user1';

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->client = static::createClient();

        $this->sponsorRepository = $this->client->getContainer()->get('app.repository.sponsor');
        $userManager = $this->client->getContainer()->get('app.manager.user');
        $this->testUser1 = $userManager->getRepository()->findOneBy(['username' => self::USER1]);
        $this->testUser2 = $userManager->getRepository()->findOneBy(['username' => self::USER2]);
        $this->client->getKernelBrowser()->loginUser($this->testUser2);
    }

    /**
     * @test
     */
    public function it_filters_sponsors_by_privacy(): void
    {
        $this->client->request('GET', '/api/sponsors?privacy='.Privacy::PRIVATE->value);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'hydra:totalItems' => $this->countPrivateSponsors(),
        ]);
    }

    /**
     * @test
     */
    public function it_only_returns_public_and_private_sponsors_created_by_user(): void
    {
        $this->client->request('GET', '/api/sponsors');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'hydra:totalItems' => $this->countPrivateSponsors() + $this->countPublicSponsors(),
        ]);
    }

    /**
     * @test
     */
    public function it_returns_404_for_private_sponsors_created_by_another_user(): void
    {
        $sponsor = $this->getRandomSponsorCreatedBy($this->testUser1);
        $this->client->request('GET', '/api/sponsors/'.$sponsor->getId()->__toString());
        $this->assertResponseStatusCodeSame(404);
    }

    private function countPrivateSponsors(): int
    {
        return (int) $this->sponsorRepository
            ->createQueryBuilder('sponsor')
            ->select('COUNT(DISTINCT sponsor.id)')
            ->andWhere('sponsor.createdBy = :user')
            ->andWhere('sponsor.privacy = :privacy')
            ->setParameter('user', $this->testUser2)
            ->setParameter('privacy', Privacy::PRIVATE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function countPublicSponsors(): int
    {
        return (int) $this->sponsorRepository
            ->createQueryBuilder('sponsor')
            ->select('COUNT(DISTINCT sponsor.id)')
            ->andWhere('sponsor.privacy = :privacy')
            ->setParameter('privacy', Privacy::PUBLIC)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getRandomSponsorCreatedBy(User $user)
    {
        return $this->sponsorRepository
            ->createQueryBuilder('sponsor')
            ->select('sponsor')
            ->andWhere('sponsor.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('sponsor.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
