<?php

declare(strict_types=1);

namespace Groshy\Tests\Functional\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

class UserControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;

    private RouterInterface $router;

    private ?ManagerInterface $userManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = $this->client->getContainer()->get(RouterInterface::class);
        $this->userManager = $this->client->getContainer()->get('app.manager.user');
    }

    /**
     * @test
     */
    public function it_shows_update_profile_for_all_users()
    {
        foreach ($this->userManager->getRepository()->findAll() as $user) {
            $this->client->loginUser($user);
            $this->client->request('GET', $this->router->generate('groshy_frontend_user_profile'));
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Profile page is broken for '.$user->getUsername());
        }
    }
}
