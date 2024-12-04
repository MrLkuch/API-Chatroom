<?php

namespace App\Entity;

use App\Repository\ChatroomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ChatroomRepository::class)]
class Chatroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['chatroom:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['chatroom:read', 'chatroom:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'chatroom')]
    #[Groups(['chatroom:read'])]
    private Collection $messages;

    /**
     * @var Collection<int, UserChatroom>
     */
    #[ORM\OneToMany(targetEntity: UserChatroom::class, mappedBy: 'chatroom')]
    #[Groups(['chatroom:read'])]
    private Collection $userChatrooms;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->userChatrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setChatroom($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChatroom() === $this) {
                $message->setChatroom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserChatroom>
     */
    public function getUserChatrooms(): Collection
    {
        return $this->userChatrooms;
    }

    public function addUserChatroom(UserChatroom $userChatroom): static
    {
        if (!$this->userChatrooms->contains($userChatroom)) {
            $this->userChatrooms->add($userChatroom);
            $userChatroom->setChatroom($this);
        }

        return $this;
    }

    public function removeUserChatroom(UserChatroom $userChatroom): static
    {
        if ($this->userChatrooms->removeElement($userChatroom)) {
            // set the owning side to null (unless already changed)
            if ($userChatroom->getChatroom() === $this) {
                $userChatroom->setChatroom(null);
            }
        }

        return $this;
    }
}
