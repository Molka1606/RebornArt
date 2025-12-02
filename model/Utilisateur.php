<?php 

class Role {
    const ADMIN = 'admin';
    const USER = 'user';
}

class User {
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $motdepasse;
    private $role; 
    private $photo;

    public function __construct($id, $nom, $prenom, $email, $motdepasse, $role = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motdepasse = $motdepasse;
        $this->photo = $photo;

        // Si le rôle est valide, on le prend, sinon par défaut user
        if ($role === Role::ADMIN || $role === Role::USER) {
            $this->role = $role;
        } else {
            $this->role = Role::USER; // rôle par défaut
        }
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        if ($role === Role::ADMIN || $role === Role::USER) {
            $this->role = $role;
        } else {
            $this->role = Role::USER; // rôle par défaut si invalide
        }
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getphoto() { return $this->photo; }
    public function setphoto($nom) { $this->photo = $photo; }
    public function setNom($nom) { $this->nom = $nom; }
    public function getPrenom() { return $this->prenom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getMotdepasse() { return $this->motdepasse; }
    public function setMotdepasse($motdepasse) { $this->motdepasse = $motdepasse; }
}

?>