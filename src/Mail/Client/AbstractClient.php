<?php

namespace LoganStellway\Base\Mail\Client;

abstract class AbstractClient implements ClientInterface
{
    protected $from = [];
    protected $to = [];
    protected $reply = [];
    protected $subject = "";

    /**
     * Reset fields
     * @return ClientInterface
     */
    public function reset(): ClientInterface
    {
        $this->to = [];
        $this->from = [];
        $this->subject = "";

        return $this;
    }

    /**
     * Add to
     * 
     * @param string $email
     * @param string $name
     * @return ClientInterface
     */
    public function addTo(string $email, string $name = null): ClientInterface
    {
        $this->to[$email] = $name;
        return $this;
    }

    /**
     * Set from address
     * 
     * @param string $email
     * @param string $name
     * @return ClientInterface
     */
    public function setFrom(string $email, string $name = null): ClientInterface
    {
        $this->from[$email] = $name;
        return $this;
    }

    /**
     * Set reply to
     * 
     * @param string $email
     * @param string $name
     * @return ClientInterface
     */
    public function setReplyTo(string $email, string $name = null): ClientInterface
    {
        $this->reply[$email] = $name;
        return $this;
    }

    /**
     * Set from address
     * 
     * @param string $subject
     * @return ClientInterface
     */
    public function setSubject(string $subject): ClientInterface
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Validate require fields are provided
     * 
     * @return bool
     * @throws Exception
     */
    protected function validateSend()
    {
        foreach (["To", "From", "Reply", "Subject"] as $field) {
            if (empty($this->{strtolower($field)})) {
                throw new \Exception("${field} not provided");
            }
        }

        return true;
    }
}
