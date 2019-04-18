<?php


namespace App\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConfirmEmailChangeSubscriber
 * @package App\EventListener
 */
class ConfirmEmailChangeSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onProfileEditSuccess(FormEvent $event)
    {
        //dd($event);
    }
}