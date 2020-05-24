<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @var int User ID
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups("infos")
     */
    protected $id;

    /**
     * @var string Username
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("infos")
     */
    protected $username;

    /**
     * @var string Email
     * @ORM\Column(type="string")
     * @Assert\Email(message="Invalid mail")
     * @Assert\NotBlank(message="Field required")
     */
    protected $email;

    /**
     * @var boolean User is a AI ?
     * @ORM\Column(type="boolean", name="ai")
     */
    protected $ai;

    /**
     * @var string Slug
     * @ORM\Column(type="string", name="slug", length=255, unique=true)
     * @Gedmo\Slug(fields={"username"}, updatable=true)
     * @Groups("infos")
     */
    protected $slug;

    /**
     * @var array Roles
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->ai = false;
    }

    /**
     * Get the user id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the username
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * Set the username
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get AI
     * @return bool
     */
    public function isAi(): bool
    {
        return $this->ai;
    }

    /**
     * Set AI
     * @param bool $ai
     * @return $this
     */
    public function setAi(bool $ai)
    {
        $this->ai = $ai;

        return $this;
    }

    /**
     * Get roles
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set roles
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = array_unique($roles);

        return $this;
    }

    /**
     * Get password
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Set password
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get salt
     * @return string|void|null
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * Clear sensitive data
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get Email
     * @return string
     */
    public function getEmail(): string
    {
        return (string) $this->email;
    }

    /**
     * Set Email
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Slug
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}
