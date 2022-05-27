<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Entity\Author;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class AuthorRemovedEvent extends DomainEvent
{
    private $id;
    private $title;

    public function __construct(int $id, string $title)
    {
        parent::__construct();
        $this->id = $id;
        $this->title = $title;
    }

    public function getEventType(): string
    {
        return 'removed';
    }

    public function getResourceKey(): string
    {
        return Author::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->id;
    }

    public function getResourceTitle(): ?string
    {
        return $this->title;
    }

    public function getResourceSecurityContext(): ?string
    {
        return Author::SECURITY_CONTEXT;
    }
}
