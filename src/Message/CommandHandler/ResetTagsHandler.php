<?php

declare(strict_types=1);

namespace Groshy\Message\CommandHandler;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Entity\Tag;
use Groshy\Entity\TagGroup;
use Groshy\Message\Command\ResetTagsCommand;
use Groshy\Provider\DefaultTagProvider;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Message\Event\NewUserEvent;
use Talav\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

final class ResetTagsHandler implements MessageHandlerInterface, MessageSubscriberInterface
{
    public function __construct(
        private AutoMapperInterface $mapper,
        private ManagerInterface $tagGroupManager,
        private ManagerInterface $tagManager,
        private UserManagerInterface $userManager,
        private MessageBusInterface $messageBus,
        private DefaultTagProvider $provider
    ) {
    }

    public static function getHandledMessages(): iterable
    {
        // handle this message on __invoke
        yield ResetTagsCommand::class;
        yield NewUserEvent::class => [
            'method' => 'handleNewUser',
        ];
    }

    public function __invoke(ResetTagsCommand $message)
    {
        $tagGroup = $this->mapper->mapToObject($message->dto, $this->tagGroupManager->create());
        $this->tagGroupManager->update($tagGroup, true);

        return $tagGroup;
    }

    public function handleNewUser(NewUserEvent $event): void
    {
        $user = $this->userManager->getRepository()->find($event->id);
        Assert::notNull($user);
        $this->addInitialTags($user);
    }

    private function addInitialTags(UserInterface $user): void
    {
        foreach ($this->provider->getTagsStructure() as $struct) {
            /** @var TagGroup $tagGroup */
            $tagGroup = $this->tagGroupManager->create();
            $tagGroup->setName($struct['name']);
            $tagGroup->setPosition($struct['position']);
            $tagGroup->setCreatedBy($user);
            $this->tagGroupManager->update($tagGroup);
            foreach ($struct['tags'] as $tagStruct) {
                /** @var Tag $tag */
                $tag = $this->tagManager->create();
                $tag->setName($tagStruct['name']);
                $tag->setPosition($tagStruct['position']);
                $tag->setColor($tagStruct['color']);
                $tag->setTagGroup($tagGroup);
                $tag->setCreatedBy($user);
                $this->tagManager->update($tag);
            }
        }
    }
}
