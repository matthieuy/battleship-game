<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 * @ORM\Table(name="players")
 */
class Player
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups("players")
     */
    private $id;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="players", fetch="EAGER")
     * @Gedmo\SortableGroup()
     */
    protected $game;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups("players")
     */
    protected $name;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1, nullable=true, options={"unsigned"=true})
     * @Groups("players")
     */
    protected $team;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     * @Groups("players")
     */
    protected $color;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1, options={"unsigned"=true})
     * @Gedmo\SortablePosition()
     * @Groups("players")
     */
    protected $position;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @Groups("players")
     */
    protected $ai;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $this->ai = false;
    }

    /**
     * Get Id player
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get Game
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * Set Game
     * @param Game $game
     *
     * @return $this
     */
    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Set Ai
     * @param bool $ai
     *
     * @return $this
     */
    public function setAi(bool $ai): self
    {
        $this->ai = $ai;

        return $this;
    }

    /**
     * Is player is AI
     * @return bool
     */
    public function isAi()
    {
        return $this->ai;
    }

    /**
     * Get User
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the user ID
     * @Groups("infos")
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user->getId();
    }

    /**
     * Set User
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->name;
    }

    /**
     * Set name
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Convert to string (name)
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->name;
    }

    /**
     * Get Position
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set Position
     * @param int $position
     *
     * @return $this
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get Color
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Set Color
     * @param string $color
     *
     * @return $this
     */
    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get Team
     * @return int
     */
    public function getTeam(): int
    {
        return $this->team;
    }

    /**
     * Set Team
     * @param int $team
     *
     * @return $this
     */
    public function setTeam(int $team): self
    {
        $this->team = min(12, max(1, intval($team)));

        return $this;
    }
}
