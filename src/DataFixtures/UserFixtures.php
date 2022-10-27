<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Message\Command\CreateUserCommand;
use Talav\Component\User\Message\Dto\CreateUserDto;

final class UserFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly UserManagerInterface $userManager
    ) {
    }

    public function loadData(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $this->messageBus->dispatch(new CreateUserCommand(new CreateUserDto(
                'user'.$i,
                'user'.$i.'@test.com',
                'user'.$i,
                $i <= 3 || $this->faker->boolean,
                $this->faker->firstName,
                $this->faker->lastName,
            )));
        }
        $this->addReferences($this->userManager);
        $this->userManager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
