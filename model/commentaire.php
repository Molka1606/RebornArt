<?php
class Commentaire {

    private ?int $id;
    private int $blog_id;
    private int $id_user;   // ✅ int
    private string $nom;
    private string $contenu;
    private string $date_pub;

    public function __construct(
        ?int $id,
        int $blog_id,
        int $id_user,
        string $nom,
        string $contenu,
        string $date_pub
    ) {
        $this->id = $id;
        $this->blog_id = $blog_id;
        $this->id_user = $id_user;
        $this->nom = $nom;
        $this->contenu = $contenu;
        $this->date_pub = $date_pub;
    }
    public function getIdBlog(): int { return $this->blog_id; }
    public function getIdUser(): int { return $this->id_user; }
    public function getAuteur(): string { return $this->nom; }
    public function getContenu(): string { return $this->contenu; }
    public function getDate(): string { return $this->date_pub; }


    public function setBlogId(int $blog_id): void { $this->blog_id = $blog_id; }
    public function setAuteur(string $auteur): void { $this->auteur = $auteur; }
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }
    public function setDate(string $date_pub): void { $this->date_pub = $date_pub; }
}
?>
