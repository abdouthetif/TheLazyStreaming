<?php

namespace App\Entity;

use App\Repository\ListeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListeRepository::class)
 */
class Liste
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_movie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_serie;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="listes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIdMovie(): ?int
    {
        return $this->id_movie;
    }

    public function setIdMovie(?int $id_movie): self
    {
        $this->id_movie = $id_movie;

        return $this;
    }

    public function getIdSerie(): ?int
    {
        return $this->id_serie;
    }

    public function setIdSerie(?int $id_serie): self
    {
        $this->id_serie = $id_serie;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
