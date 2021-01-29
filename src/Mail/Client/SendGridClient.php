<?php

namespace LoganStellway\Base\Mail\Client;

use function Env\env;
use LoganStellway\Base\Exception\ApiResponseError;
use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Response as SendGridResponse;

class SendGridClient extends AbstractClient
{
    /**
     * @var SendGrid
     */
    protected $client;

    /**
     * @var array
     */
    protected $substitutions = [];

    /**
     * Get SendGrid instance
     */
    public function getClient(array $options = [])
    {
        if (!$this->client) {
            $this->client = new SendGrid(
                env("SENDGRID_API_KEY"),
                $options
            );
        }
        return $this->client;
    }

    /**
     * Send email by template ID
     * 
     * @param string $templateId
     * @param array $substitutions
     */
    public function sendTemplate(string $templadId, array $substitutions = null): ClientInterface
    {
        if ($this->validateSend()) {
            // Notification
            $message = new Mail();

            // Set template ID
            $message->setTemplateId($templadId);

            // Add from address
            foreach ($this->from as $email => $name) {
                $message->setFrom($email, $name);
            }

            // Add to addresses
            foreach ($this->to as $email => $name) {
                $message->addTo($email, $name);
            }

            // Add to addresses
            foreach ($this->reply as $email => $name) {
                $message->setReplyTo($email, $name);
            }

            // Add substitution data
            $message->addSubstitutions($substitutions);

            // Set subject
            $message->setSubject($this->subject);

            // Send email
            $this->getClient()->send($message);
        }
        return $this;
    }

    /**
     * Handle response
     * 
     * @param SendGridResponse $response
     * @return mixed
     */
    public function handleApiResponse(SendGridResponse $response)
    {
        $body = $response->body();

        if (!$response->statusCode() || $response->statusCode() >= 400) {
            throw new ApiResponseError($body);
        }

        return json_decode($body, true);
    }

    /**
     * Get lists
     * 
     * @param bool $report
     * @return mixed
     */
    public function getLists(bool $report = false)
    {
        return $this->handleApiResponse($this->getClient()->client->marketing()->lists()->get(), $report);
    }

    /**
     * Get segments
     * 
     * @param bool $report
     * @return mixed
     */
    public function getSegments(bool $report = false)
    {
        return $this->handleApiResponse($this->getClient()->client->marketing()->segments()->get(), $report);
    }

    /**
     * Get suppression groups
     * 
     * @param bool $report
     * @return mixed
     */
    public function getSuppressionGroups(bool $report = false)
    {
        return $this->handleApiResponse($this->getClient()->client->asm()->groups()->get(), $report);
    }

    /**
     * Add contacts
     *
     * @param array $data
     * @param string $listId
     * @param bool $report
     * @return mixed
     */
    public function addContacts(array $data, $listId, bool $report = false)
    {
        return $this->handleApiResponse($this->getClient()->client->marketing()->contacts()->put([
            'list_ids' => [$listId],
            'contacts' => $data,
        ]), $report);
    }

    /**
     * Add single contact
     *
     * @param array $data
     * @param string $listId
     * @param bool $report
     * @return mixed
     */
    public function addContact(array $data, $listId, bool $report = false)
    {
        return $this->addContacts([$data], $listId, $report);
    }

    /**
     * Unsubscribe email
     *
     * @param string|array $emails
     * @param int $id
     * @param bool $report
     * @return mixed
     */
    public function unsubscribe($emails, $id, bool $report = false)
    {
        if (!is_array($emails)) {
            $emails = [$emails];
        }

        return $this->handleApiResponse($this->getClient()->client->asm()->groups()->$id()->suppressions()->post([
            'recipient_emails' => $emails
        ]));
    }

    /**
     * Validate email
     *
     * @param string $email
     * @param string $source
     * @param bool $report
     * @todo ensure this method is working properly using an account with privileges
     */
    public function validateEmail(string $email, string $source = '', bool $report = false)
    {
        return $this->handleApiResponse($this->getClient()->client->validations()->email()->post(
            compact('email', 'source')
        ));
    }
}
