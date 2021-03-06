<?php


namespace App\EventSubcriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class PasswordHashSubscriber implements EventSubscriberInterface
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents()
    {
        return [
          KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE]
        ];
    }

    public function hashPassword(ViewEvent $event) {
            $user = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();

            if (!$user instanceof User ||
                !in_array($method, [Request::METHOD_POST,Request::METHOD_PUT])) {
                return;
            }

            //it is an user, we need to hash password here
            $user->setPassword(
              $this->passwordEncoder->encodePassword($user,$user->getPassword())
            );
    }
}
