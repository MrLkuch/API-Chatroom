<?php

namespace App\Entity;

use App\Repository\UserChatroomRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserChatroomRepository::class)]
class UserChatroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['userchatroom:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userChatrooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userchatroom:read'])]
    private ?User $_user = null;

    #[ORM\ManyToOne(inversedBy: 'userChatrooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userchatroom:read'])]
    private ?Chatroom $chatroom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['userchatroom:read', 'userchatroom:write'])]
    private ?\DateTimeInterface $lastRead = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function setUser(?User $_user): static
    {
        $this->_user = $_user;

        return $this;
    }

    public function getChatroom(): ?Chatroom
    {
        return $this->chatroom;
    }

    public function setChatroom(?Chatroom $chatroom): static
    {
        $this->chatroom = $chatroom;

        return $this;
    }

    public function getLastRead(): ?\DateTimeInterface
    {
        return $this->lastRead;
    }

    public function setLastRead(?\DateTimeInterface $lastRead): static
    {
        $this->lastRead = $lastRead;

        return $this;
    }

    #[Groups(['userchatroom:read'])]
    public function getUserId(): ?int
    {
        return $this->_user ? $this->_user->getId() : null;
    }

    #[Groups(['userchatroom:read'])]
    public function getChatroomId(): ?int
    {
        return $this->chatroom ? $this->chatroom->getId() : null;
    }
}
