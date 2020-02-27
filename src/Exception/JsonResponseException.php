<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonResponseException extends HttpException
{
    /**
     * @var array|null
     */
    private ?array $messages = null;

    /**
     * @param int         $statusCode
     * @param string|null $message
     */
    public function __construct(int $statusCode, string $message = null)
    {
        parent::__construct($statusCode, $message);
        $this->setHeaders(['Content-Type' => 'application/problem+json']);
    }

    /**
     * @return array|null
     */
    public function getMessages(): ?array
    {
        return $this->messages;
    }

    /**
     * @param array|null $messages
     *
     * @return self
     */
    public function setMessages(?array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }
}
