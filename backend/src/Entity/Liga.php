<?php

namespace App\Entity;

use App\Repository\LigaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\UsuarioFantasy;
use App\Entity\Usuario;

#[ORM\Entity(repositoryClass: LigaRepository::class)]
class Liga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?bool $privada = null;

    /**
     * @var Collection<int, UsuarioFantasy>
     */
    #[ORM\OneToMany(targetEntity: UsuarioFantasy::class, mappedBy: 'liga', cascade: ['remove'])]
    private Collection $usuarioFantasies;

    /**
     * @var Collection<int, Solicitud>
     */
    #[ORM\OneToMany(targetEntity: Solicitud::class, mappedBy: 'liga')]
    private Collection $solicituds;

    #[ORM\Column]
    private ?int $max_miembros = null;

    /**
     * @var Collection<int, ChatLiga>
     */
    #[ORM\OneToMany(targetEntity: ChatLiga::class, mappedBy: 'liga')]
    private Collection $chatLigas;

    public function __construct()
    {
        $this->usuarioFantasies = new ArrayCollection();
        $this->solicituds = new ArrayCollection();
        $this->chatLigas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getMiembros(): ?int
    {
        return $this->usuarioFantasies->count();
    }

    public function isPrivada(): ?bool
    {
        return $this->privada;
    }

    public function setPrivada(bool $privada): static
    {
        $this->privada = $privada;

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
            $usuarioFantasy->setLiga($this);
        }

        return $this;
    }

    public function removeUsuarioFantasy(UsuarioFantasy $usuarioFantasy): static
    {
        if ($this->usuarioFantasies->removeElement($usuarioFantasy)) {
            // set the owning side to null (unless already changed)
            if ($usuarioFantasy->getLiga() === $this) {
                $usuarioFantasy->setLiga(null);
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

    public function getMaxMiembros(): ?int
    {
        return $this->max_miembros;
    }

    public function setMaxMiembros(int $max_miembros): static
    {
        $this->max_miembros = $max_miembros;

        return $this;
    }

    /**
     * @return Collection<int, ChatLiga>
     */
    public function getChatLigas(): Collection
    {
        return $this->chatLigas;
    }

    public function addChatLiga(ChatLiga $chatLiga): static
    {
        if (!$this->chatLigas->contains($chatLiga)) {
            $this->chatLigas->add($chatLiga);
            $chatLiga->setLiga($this);
        }

        return $this;
    }

    public function removeChatLiga(ChatLiga $chatLiga): static
    {
        if ($this->chatLigas->removeElement($chatLiga)) {
            // set the owning side to null (unless already changed)
            if ($chatLiga->getLiga() === $this) {
                $chatLiga->setLiga(null);
            }
        }

        return $this;
    }

}
