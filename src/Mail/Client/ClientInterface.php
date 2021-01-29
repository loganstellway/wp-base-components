<?php

namespace LoganStellway\Base\Mail\Client;

interface ClientInterface
{
    public function reset(): ClientInterface;
    public function addTo(string $email, string $name = null): ClientInterface;
    public function setFrom(string $email, string $name = null): ClientInterface;
    public function setReplyTo(string $email, string $name = null): ClientInterface;
    public function setSubject(string $subject): ClientInterface;
}
