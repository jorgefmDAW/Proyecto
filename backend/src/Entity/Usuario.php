<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 40)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, ForoGlobal>
     */
    #[ORM\OneToMany(targetEntity: ForoGlobal::class, mappedBy: 'id_usuario')]
    private Collection $foroGlobals;

    /**
     * @var Collection<int, UsuarioFantasy>
     */
    #[ORM\OneToMany(targetEntity: UsuarioFantasy::class, mappedBy: 'usuario')]
    private Collection $usuarioFantasies;

    /**
     * @var Collection<int, Solicitud>
     */
    #[ORM\ManyToMany(targetEntity: Solicitud::class, mappedBy: 'id_usuario')]
    private Collection $solicituds;

    public function __construct()
    {
        $this->foroGlobals = new ArrayCollection();
        $this->usuarioFantasies = new ArrayCollection();
        $this->solicituds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * @return Collection<int, ForoGlobal>
     */
    public function getForoGlobals(): Collection
    {
        return $this->foroGlobals;
    }

    public function addForoGlobal(ForoGlobal $foroGlobal): static
    {
        if (!$this->foroGlobals->contains($foroGlobal)) {
            $this->foroGlobals->add($foroGlobal);
            $foroGlobal->setUsuario($this);
        }

        return $this;
    }

    public function removeForoGlobal(ForoGlobal $foroGlobal): static
    {
        if ($this->foroGlobals->removeElement($foroGlobal)) {
            // set the owning side to null (unless already changed)
            if ($foroGlobal->getUsuario() === $this) {
                $foroGlobal->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsuarioFantasy>
     */
    public function getUsuarioFantasies(): Collection
    {
        return $this->usuarioFantasies;
    }

    public function addUsuarioFantasy(UsuarioFantasy $usuarioFantasy): static
    {
        if (!$this->usuarioFantasies->contains($usuarioFantasy)) {
            $this->usuarioFantasies->add($usuarioFantasy);
            $usuarioFantasy->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioFantasy(UsuarioFantasy $usuarioFantasy): static
    {
        if ($this->usuarioFantasies->removeElement($usuarioFantasy)) {
            // set the owning side to null (unless already changed)
            if ($usuarioFantasy->getUsuario() === $this) {
                $usuarioFantasy->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Solicitud>
     */
    public function getSolicituds(): Collection
    {
        return $this->solicituds;
    }

}
