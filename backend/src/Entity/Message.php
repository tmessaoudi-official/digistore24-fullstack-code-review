<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message
{
    use TimestampableEntity;

    public const string STATUS_SENT = 'sent';
    public const string STATUS_RECEIVED = 'received';
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_SENT;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Message $inReplyTo = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'inReplyTo')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $replies;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
        $this->status = self::STATUS_SENT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getInReplyTo(): ?self
    {
        return $this->inReplyTo;
    }

    public function setInReplyTo(?self $inReplyTo): static
    {
        $this->inReplyTo = $inReplyTo;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->content,
            'user' => $this->user?->getName(),
            'status' => $this->status,
            'created_at' => $this->createdAt?->format('c'),
            'updated_at' => $this->updatedAt?->format('c'),
            'in_reply_to' => $this->inReplyTo?->getId(),
            'replies' => $this->replies?->map(static fn ($reply) => $reply->toArray() ?? [])->toArray() ?? [],
        ];
    }
}
