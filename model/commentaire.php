<?php
class Commentaire {
    private ?int $id;          // ID du commentaire
    private ?int $blog_id;     // ID du blog
    private ?string $auteur;   
    private ?string $contenu;  
    private ?string $date_pub;

    public function __construct(?int $id, ?int $blog_id, ?string $auteur, ?string $contenu, ?string $date_pub) {
        $this->id = $id;
        $this->blog_id = $blog_id;
        $this->auteur = $auteur;
        $this->contenu = $contenu;
        $this->date_pub = $date_pub;
    }

    public function getId(): ?int { return $this->id; }
    public function getIdBlog(): ?int { return $this->blog_id; }
    public function getAuteur(): ?string { return $this->auteur; }
    public function getContenu(): ?string { return $this->contenu; }
    public function getDate(): ?string { return $this->date_pub; }

    public function setBlogId(int $blog_id): void { $this->blog_id = $blog_id; }
    public function setAuteur(string $auteur): void { $this->auteur = $auteur; }
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }
    public function setDate(string $date_pub): void { $this->date_pub = $date_pub; }
}
?>
