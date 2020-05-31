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
    protected $id;

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
     * @var int
     * @ORM\Column(type="smallint", length=2, options={"unsigned"=true})
     * @Groups("run")
     */
    protected $life;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @Groups("run")
     */
    protected $score;

    /**
     * @var array<mixed>
     * @ORM\Column(type="json", nullable=true)
     */
    protected $boats;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $this->ai = false;
        $this->life = 0;
        $this->score = 0;
        $this->boats = [];
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
    public function isAi(): bool
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
     * @Groups("players")
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

    /**
     * Get Life
     * @return int
     */
    public function getLife(): int
    {
        return $this->life;
    }

    /**
     * Is player is alive
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->life > 0;
    }

    /**
     * Set Life
     * @param int $life
     *
     * @return $this
     */
    public function setLife(int $life): self
    {
        $this->life = $life;

        return $this;
    }

    /**
     * Remove a life
     * @return $this
     */
    public function removeLife(): self
    {
        $this->life--;

        return $this;
    }

    /**
     * Get Boats
     * @return array<mixed>
     */
    public function getBoats(): array
    {
        return $this->boats;
    }

    /**
     * Set Boats
     * @param array<int> $boats
     *
     * @return $this
     */
    public function setBoats(array $boats): self
    {
        $this->boats = $boats;

        return $this;
    }

    /**
     * Get Score
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Set Score
     * @param int $score
     *
     * @return $this
     */
    public function setScore(int $score): self
    {
        $this->score = $score;

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
}
