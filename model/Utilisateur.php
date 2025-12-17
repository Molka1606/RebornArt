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
    private $reset_code;
    private $status;
    private $telephone;
    private $date_naissance;

    // ✅ CONSTRUCTEUR CORRECT AVEC telephone ET date_naissance
    public function __construct(
        $id,
        $nom,
        $prenom,
        $email,
        $motdepasse,
        $role = 'user',
        $photo = null,
        $telephone = null,
        $date_naissance = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motdepasse = $motdepasse;
        $this->photo = $photo;
        $this->telephone = $telephone;
        $this->date_naissance = $date_naissance;
        $this->status = 'active';

        // Vérification du rôle
        if ($role === Role::ADMIN || $role === Role::USER) {
            $this->role = $role;
        } else {
            $this->role = Role::USER;
        }
    }

    // ----- GETTERS -----
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getMotdepasse() { return $this->motdepasse; }
    public function getRole() { return $this->role; }
    public function getPhoto() { return $this->photo; }
    public function getStatus() { return $this->status; }
    public function getTelephone() { return $this->telephone; }
    public function getDateNaissance() { return $this->date_naissance; }

    // ----- SETTERS -----
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMotdepasse($motdepasse) { $this->motdepasse = $motdepasse; }
    public function setPhoto($photo) { $this->photo = $photo; }
    public function setStatus($status) { $this->status = $status; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setDateNaissance($date) { $this->date_naissance = $date; }

    public function setRole($role) {
        if ($role === Role::ADMIN || $role === Role::USER) {
            $this->role = $role;
        }
    }
}

?>