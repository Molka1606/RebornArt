<?php

class Blog {

    private ?int $id;
    private string $titre;
    private string $contenu;
    private ?string $image;
    private DateTimeImmutable $date_pub;

    public function __construct(
        ?int $id,
        string $titre,
        string $contenu,
        ?string $image = null,
        $date = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->image = $image;

        if ($date instanceof DateTimeInterface) {
            $this->date_pub = DateTimeImmutable::createFromInterface($date);
        } elseif (is_string($date) && trim($date) !== '') {
            $this->date_pub = new DateTimeImmutable($date);
        } else {
            $this->date_pub = new DateTimeImmutable();
        }
    }

    // ===== GETTERS =====
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getContenu(): string {
        return $this->contenu;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function getDate(): string {
        return $this->date_pub->format('Y-m-d');
    }

    // ===== SETTERS =====
    public function setImage(?string $image): void {
        $this->image = $image;
    }

    public function setDate($date): void {
        if ($date instanceof DateTimeInterface) {
            $this->date_pub = DateTimeImmutable::createFromInterface($date);
        } elseif (is_string($date) && trim($date) !== '') {
            $this->date_pub = new DateTimeImmutable($date);
        }
    }
}
