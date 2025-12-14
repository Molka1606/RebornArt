<?php

class blog {
    private ?int $id;
    private ?string $titre;
    private ?string $contenu;
    private ?DateTimeImmutable $date_pub;

    public function __construct(?int $id, string $titre, string $contenu, $date = null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;

        if ($date instanceof \DateTimeInterface) {
            $this->date_pub = \DateTimeImmutable::createFromMutable(
                \DateTime::createFromInterface($date)
            );
        } elseif (is_string($date) && trim($date) !== '') {
            $this->date_pub = new \DateTimeImmutable($date);
        } else {
            $this->date_pub = null;
        }
    }

    // ======================
    //       GETTERS
    // ======================

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getContenu(): string {
        return $this->contenu;
    }

    public function getDate(): string {
        if ($this->date_pub instanceof \DateTimeInterface) {
            return $this->date_pub->format('Y-m-d');
        }
        return date('Y-m-d');
    }

    // ======================
    //       SETTERS
    // ======================

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setTitre(string $titre): void {
        $this->titre = $titre;
    }

    public function setContenu(string $contenu): void {
        $this->contenu = $contenu;
    }

    public function setDate($date): void {
        if ($date instanceof \DateTimeInterface) {
            $this->date_pub = \DateTimeImmutable::createFromMutable(
                \DateTime::createFromInterface($date)
            );
        } elseif (is_string($date) && trim($date) !== '') {
            $this->date_pub = new \DateTimeImmutable($date);
        } else {
            $this->date_pub = null;
        }
    }
}
