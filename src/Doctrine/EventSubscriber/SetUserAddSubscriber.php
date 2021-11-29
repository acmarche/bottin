<?php

namespace AcMarche\Bottin\Doctrine\EventSubscriber;

use Symfony\Component\Security\Core\User\UserInterface;
use AcMarche\Bottin\Utils\PropertyUtil;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Symfony\Component\Security\Core\Security;

final class SetUserAddSubscriber implements EventSubscriber
{
    private Security $security;
    private PropertyUtil $propertyUtil;

    public function __construct(Security $security, PropertyUtil $propertyUtil)
    {
        $this->security = $security;
        $this->propertyUtil = $propertyUtil;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $object = $lifecycleEventArgs->getObject();
        if (!$this->propertyUtil->getPropertyAccessor()->isWritable($object, 'userAdd')) {
            return;
        }

        $this->setUserAdd($object);
    }

    private function setUserAdd(object $entity): void
    {
        //for loading fixtures
        if ($entity->getUserAdd()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new Exception('You must be login');
        }

        if ($user) {
            $entity->setUserAdd($user->getUserIdentifier());
        }
    }
}
