<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Entity\Author;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class AuthorModifiedEvent extends DomainEvent
{
    private $author;
    private $payload;

    public function __construct(Author $author, array $payload)
    {
        parent::__construct();
        $this->author = $author;
        $this->payload = $payload;
    }

    public function getProgramme(): Author
    {
        return $this->author;
    }

    public function getEventPayload(): ?array
    {
        return $this->payload;
    }

    public function getEventType(): string
    {
        return 'modified';
    }

    public function getResourceKey(): string
    {
        return Author::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->author->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->author->getName();
    }

    public function getResourceSecurityContext(): ?string
    {
        return Author::SECURITY_CONTEXT;
    }
}
