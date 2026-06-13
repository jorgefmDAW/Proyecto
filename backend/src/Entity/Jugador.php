<<<<<<< HEAD
<?php

namespace App\Entity;

use App\Enum\PosicionEnum;
use App\Repository\JugadorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JugadorRepository::class)]
class Jugador
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $posicion = null;

    #[ORM\ManyToOne(inversedBy: 'jugadors')]
    private ?Equipo $equipo = null;

    /**
     * @var Collection<int, Puntuacion>
     */
    #[ORM\OneToMany(targetEntity: Puntuacion::class, mappedBy: 'jugador')]
    private Collection $puntuacions;

    #[ORM\Column]
    private ?int $edad = null;

    #[ORM\Column(length: 255)]
    private ?string $nacionalidad = null;

    /**
     * @var Collection<int, EleccionEstrella>
     */
    #[ORM\OneToMany(targetEntity: EleccionEstrella::class, mappedBy: 'jugador')]
    private Collection $eleccionEstrellas;

    #[ORM\Column(length: 255)]
    private ?string $foto = null;


    public function __construct()
    {
        $this->puntuacions = new ArrayCollection();
        $this->eleccionEstrellas = new ArrayCollection();
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

    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): static
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * @return Collection<int, Puntuacion>
     */
    public function getPuntuacions(): Collection
    {
        return $this->puntuacions;
    }

    public function addPuntuacion(Puntuacion $puntuacion): static
    {
        if (!$this->puntuacions->contains($puntuacion)) {
            $this->puntuacions->add($puntuacion);
            $puntuacion->setJugador($this);
        }

        return $this;
    }

    public function removePuntuacion(Puntuacion $puntuacion): static
    {
        if ($this->puntuacions->removeElement($puntuacion)) {
            // set the owning side to null (unless already changed)
            if ($puntuacion->getJugador() === $this) {
                $puntuacion->setJugador(null);
            }
        }

        return $this;
    }

    public function getPosicion(): ?string
    {
        return $this->posicion;
    }

    public function setPosicion(string $posicion): static
    {
        $this->posicion = $posicion;

        return $this;
    }

    public function sumaPuntos(): int
    {
        $suma = 0;
        
        foreach ($this->getPuntuacions() as $puntuacion) {
            $suma += $puntuacion->getPuntos();
        }

        return $suma;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): static
    {
        $this->edad = $edad;

        return $this;
    }

    public function getNacionalidad(): ?string
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(string $nacionalidad): static
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    /**
     * @return Collection<int, EleccionEstrella>
     */
    public function getEleccionEstrellas(): Collection
    {
        return $this->eleccionEstrellas;
    }

    public function addEleccionEstrella(EleccionEstrella $eleccionEstrella): static
    {
        if (!$this->eleccionEstrellas->contains($eleccionEstrella)) {
            $this->eleccionEstrellas->add($eleccionEstrella);
            $eleccionEstrella->setJugador($this);
        }

        return $this;
    }

    public function removeEleccionEstrella(EleccionEstrella $eleccionEstrella): static
    {
        if ($this->eleccionEstrellas->removeElement($eleccionEstrella)) {
            // set the owning side to null (unless already changed)
            if ($eleccionEstrella->getJugador() === $this) {
                $eleccionEstrella->setJugador(null);
            }
        }

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }
}
=======
<?php

namespace App\Entity;

use App\Enum\PosicionEnum;
use App\Repository\JugadorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JugadorRepository::class)]
class Jugador
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $posicion = null;

    #[ORM\ManyToOne(inversedBy: 'jugadors')]
    private ?Equipo $equipo = null;

    /**
     * @var Collection<int, Puntuacion>
     */
    #[ORM\OneToMany(targetEntity: Puntuacion::class, mappedBy: 'jugador')]
    private Collection $puntuacions;

    #[ORM\Column]
    private ?int $edad = null;

    #[ORM\Column(length: 255)]
    private ?string $nacionalidad = null;

    #[ORM\Column(length: 255)]
    private ?string $foto = null;

    /**
     * @var Collection<int, EleccionFantasy>
     */
    #[ORM\OneToMany(targetEntity: EleccionFantasy::class, mappedBy: 'jugador')]
    private Collection $eleccionFantasies;


    public function __construct()
    {
        $this->puntuacions = new ArrayCollection();
        $this->eleccionFantasies = new ArrayCollection();
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

    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): static
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * @return Collection<int, Puntuacion>
     */
    public function getPuntuacions(): Collection
    {
        return $this->puntuacions;
    }

    public function addPuntuacion(Puntuacion $puntuacion): static
    {
        if (!$this->puntuacions->contains($puntuacion)) {
            $this->puntuacions->add($puntuacion);
            $puntuacion->setJugador($this);
        }

        return $this;
    }

    public function removePuntuacion(Puntuacion $puntuacion): static
    {
        if ($this->puntuacions->removeElement($puntuacion)) {
            // set the owning side to null (unless already changed)
            if ($puntuacion->getJugador() === $this) {
                $puntuacion->setJugador(null);
            }
        }

        return $this;
    }

    public function getPosicion(): ?string
    {
        return $this->posicion;
    }

    public function setPosicion(string $posicion): static
    {
        $this->posicion = $posicion;

        return $this;
    }

    public function sumaPuntos(): int
    {
        $suma = 0;
        
        foreach ($this->getPuntuacions() as $puntuacion) {
            $suma += $puntuacion->getPuntos();
        }

        return $suma;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): static
    {
        $this->edad = $edad;

        return $this;
    }

    public function getNacionalidad(): ?string
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(string $nacionalidad): static
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * @return Collection<int, EleccionFantasy>
     */
    public function getEleccionFantasies(): Collection
    {
        return $this->eleccionFantasies;
    }

    public function addEleccionFantasy(EleccionFantasy $eleccionFantasy): static
    {
        if (!$this->eleccionFantasies->contains($eleccionFantasy)) {
            $this->eleccionFantasies->add($eleccionFantasy);
            $eleccionFantasy->setJugador($this);
        }

        return $this;
    }

    public function removeEleccionFantasy(EleccionFantasy $eleccionFantasy): static
    {
        if ($this->eleccionFantasies->removeElement($eleccionFantasy)) {
            // set the owning side to null (unless already changed)
            if ($eleccionFantasy->getJugador() === $this) {
                $eleccionFantasy->setJugador(null);
            }
        }

        return $this;
    }
}
>>>>>>> main
