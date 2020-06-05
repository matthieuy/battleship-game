<?php

namespace App\Entity;

use App\Boats;
use App\GameHelper\Box;
use App\Repository\GameRepository;
use DateTime as DateTimeAlias;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Game
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\Table(name="games")
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Game
{
    // Status
    public const STATUS_WAIT = 0;
    public const STATUS_RUN  = 1;
    public const STATUS_END  = 2;

    // Number of player => size of grid (default k=0)
    protected $sizeList = [
        0 => 50,
        2 => 15,
        3 => 20,
        4 => 25,
        5 => 25,
        6 => 30,
        7 => 30,
        8 => 35,
        9 => 40,
        10 => 45,
    ];

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups("infos")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(
     *     min="4",
     *     max="128",
     *     minMessage="The game's name length must be greather than {{ limit }} characters",
     *     maxMessage="The game's name length must be less than {{ limit }} characters"
     * )
     * @Groups("infos")
     */
    protected $name;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1, options={"unsigned"=true})
     * @Groups("infos")
     */
    protected $status;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1, options={"unsigned"=true})
     * @Assert\Range(
     *     min="2",
     *     max="12",
     *     minMessage="The number of player must be greater then {{ limit }}",
     *     maxMessage="The number of player must be under {{ limit }}"
     * )
     * @Groups("infos")
     */
    protected $maxPlayer;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @Assert\Range(
     *     min="15",
     *     max="50",
     *     minMessage="The grid size must be greater then {{ limit }}x{{ limit }}",
     *     maxMessage="The grid size must be under {{ limit }}x{{ limit }}"
     * )
     * @Groups({"infos", "run"})
     */
    protected $size;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @Groups("infos")
     */
    protected $creator;

    /**
     * @var DateTimeAlias
     * @ORM\Column(type="datetime")
     * @Groups("infos")
     */
    protected $createAt;

    /**
     * @var ArrayCollection|Player[]
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="game", cascade={"remove"})
     * @ORM\OrderBy({"position": "ASC"})
     * @Groups("players")
     */
    protected $players;

    /**
     * @var array<mixed>
     * @ORM\Column(type="json")
     * @Groups({"infos", "run"})
     */
    protected $options;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=200)
     * @Gedmo\Slug(fields={"name"})
     * @Groups("infos")
     */
    protected $slug;

    /**
     * @var DateTimeAlias
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $runAt;

    /**
     * @var DateTimeAlias
     * @ORM\Column(type="datetime", name="last", nullable=true)
     */
    protected $lastShoot;

    /**
     * @var array<int>
     * @ORM\Column(type="simple_array", nullable=true)
     * @Groups("run")
     */
    protected $tour;

    /**
     * @var array<mixed>
     * @ORM\Column(type="array", nullable=true)
     */
    protected $grid;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->status = self::STATUS_WAIT;
        $this->maxPlayer = 4;
        $this->createAt = new DateTimeAlias();
        $this->options = [];
        $this->grid = [];
        $this->tour = [];
    }

    /**
     * Get ID
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * Set Name
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Slug
     * @return string
     */
    public function getSlug(): string
    {
        return (string) $this->slug;
    }

    /**
     * Get MaxPlayer
     * @return int
     */
    public function getMaxPlayer(): int
    {
        return (int) $this->maxPlayer;
    }

    /**
     * Set MaxPlayer
     * @param int $maxPlayer
     *
     * @return $this
     */
    public function setMaxPlayer(int $maxPlayer): self
    {
        $this->maxPlayer = min(12, max(2, intval($maxPlayer)));

        return $this;
    }

    /**
     * Get Creator
     * @return User
     */
    public function getCreator(): User
    {
        return $this->creator;
    }

    /**
     * Set Creator
     * @param User $creator
     *
     * @return $this
     */
    public function setCreator(User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Check if a user is the creator of game
     * @param User|null $user
     *
     * @return bool
     */
    public function isCreator(?User $user = null): bool
    {
        return $user !== null && $this->creator->getId() === $user->getId();
    }

    /**
     * Get Status
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set Status
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Is game is over ?
     * @return bool
     *
     * @Groups("run")
     */
    public function isFinished(): bool
    {
        return $this->status === self::STATUS_END;
    }

    /**
     * Get CreateAt
     * @return DateTimeAlias
     */
    public function getCreateAt(): DateTimeAlias
    {
        return $this->createAt;
    }

    /**
     * Get Size
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set Size
     * @param int $size
     *
     * @return $this
     */
    public function setSize(int $size): self
    {
        $this->size = min($this->sizeList[0], max($this->sizeList[2], $size));

        return $this;
    }

    /**
     * Set size by player number
     * @param int|null $playerNb
     *
     * @return $this
     */
    public function setSizeByPlayersNb(?int $playerNb = null): self
    {
        $size = ($playerNb !== null && array_key_exists($playerNb, $this->sizeList)) ? $this->sizeList[$playerNb]: $this->sizeList[0];
        $this->setSize($size);

        return $this;
    }

    /**
     * Get Players
     * @return Player[]|ArrayCollection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set Players
     * @param Player[]|ArrayCollection $players
     *
     * @return $this
     */
    public function setPlayers(array $players): self
    {
        $this->players = $players;

        return $this;
    }

    /**
     * Is player is in game
     * @param Player $player
     *
     * @return bool
     */
    public function hasPlayer(Player $player): bool
    {
        return $this->players->contains($player);
    }

    /**
     * Add a player
     * @param Player $player
     *
     * @return $this
     */
    public function addPlayer(Player $player): self
    {
        if (!$this->hasPlayer($player)) {
            $player->setGame($this);
            $this->players->add($player);
        }

        return $this;
    }

    /**
     * Remove a player
     * @param Player $player
     *
     * @return $this
     */
    public function removePlayer(Player $player): self
    {
        if ($this->hasPlayer($player)) {
            $this->players->removeElement($player);
        }

        return $this;
    }

    /**
     * Get player by user
     * @param User $user
     *
     * @return Player|null
     */
    public function getPlayerByUser(User $user): ?Player
    {
        foreach ($this->players as $player) {
            if ($player->getUserId() === $user->getId()) {
                return $player;
            }
        }

        return null;
    }

    /**
     * Get Options
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get a option value
     * @param string $name    The name of the option
     * @param mixed  $default Default value
     *
     * @return mixed The value
     */
    public function getOption(string $name, $default = false)
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    /**
     * Set Options
     * @param array<mixed> $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set a option
     * @param string $name  The name of the option
     * @param mixed  $value Value
     *
     * @return $this
     */
    public function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Remove a option
     * @param string $name The name of the option
     *
     * @return $this
     */
    public function removeOption(string $name): self
    {
        if (array_key_exists($name, $this->options)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    /**
     * Get Grid
     * @return array<mixed>
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * Set Grid
     * @param array<mixed> $grid
     *
     * @return $this
     */
    public function setGrid(array $grid): self
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * Get RunAt
     * @return DateTimeAlias
     */
    public function getRunAt(): DateTimeAlias
    {
        return $this->runAt;
    }

    /**
     * Set RunAt
     * @param DateTimeAlias $dateTime
     *
     * @return $this
     */
    public function setRunAt(DateTimeAlias $dateTime): self
    {
        $this->runAt = $dateTime;

        return $this;
    }

    /**
     * Get LastShoot
     * @return DateTimeAlias
     */
    public function getLastShoot(): DateTimeAlias
    {
        return $this->lastShoot;
    }

    /**
     * Set LastShoot
     * @param DateTimeAlias|null $lastShoot
     *
     * @return $this
     */
    public function setLastShoot(?DateTimeAlias $lastShoot = null): self
    {
        if (!$lastShoot) {
            $lastShoot = new DateTimeAlias('now');
        }
        $this->lastShoot = $lastShoot;

        return $this;
    }

    /**
     * Get Tour
     * @return array<int>
     */
    public function getTour(): array
    {
        return $this->tour;
    }

    /**
     * Set Tour
     * @param array<int> $tour
     *
     * @return $this
     */
    public function setTour(array $tour): self
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get players from team number
     * @param int  $team      Team number
     * @param bool $aliveOnly Only return alive player
     *
     * @return array<Player>
     */
    public function getPlayersByTeam(int $team, bool $aliveOnly = true): array
    {
        $players = [];
        foreach ($this->players as $player) {
            // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
            if ($player->getTeam() === $team && (!$aliveOnly || ($aliveOnly && $player->isAlive()))) {
                $players[] = $player;
            }
        }

        return $players;
    }

    /**
     * Get player by position
     * @param int $position
     *
     * @return Player|null
     */
    public function getPlayerByPosition(int $position): ?Player
    {
        $playerFind = null;
        foreach ($this->players as $player) {
            if ($position === $player->getPosition()) {
                $playerFind = $player;
                // phpcs:disable Squiz.WhiteSpace.ControlStructureSpacing.SpacingBeforeClose
                // phpcs:disable SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing.IncorrectLinesCountAfterLastControlStructure
                break;
            }
        }

        return $playerFind;
    }

    /**
     * Save box to grid
     * @param Box $box
     *
     * @return $this
     */
    public function saveBox(Box $box): self
    {
        $this->grid[$box->getY()][$box->getX()] = $box->getInfosToReturn(true);

        return $this;
    }

    /**
     * Update grid after sink a boat
     * @param Box $box
     * @param int $boatLength
     *
     * @return array<mixed>
     */
    public function updateSink(Box $box, int $boatLength): array
    {
        $this->saveBox($box);
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $b) {
                if (array_key_exists('boat', $b) && $b['boat'] === $box->getBoat()) {
                    $this->grid[$y][$x]['img'] = Boats::getDeadImg($b['img'], $boatLength);
                    $this->grid[$y][$x]['dead'] = true;
                    unset($this->grid[$y][$x]['explose']);
                }
            }
        }

        return $this->grid;
    }

    /**
     * Convert to string (slug)
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->slug;
    }
}
