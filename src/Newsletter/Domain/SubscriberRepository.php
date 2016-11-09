<?php

namespace Newsletter\Domain;

interface SubscriberRepository
{
    /**
     * @param EmailAddress $emailAddress
     * @return Subscriber
     */
    public function getByEmailAddress($emailAddress);

    /**
     * @param Subscriber $subscriber
     */
    public function save(Subscriber $subscriber);

    /**
     * @return Subscriber[]
     */
    public function getAll();
}
