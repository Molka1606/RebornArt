<?php 
require __DIR__. "/../model/config.php";
require __DIR__. "/../model/Utilisateur.php";

class userController {

    function getAllUser(){
        $sql="SELECT * FROM User";
        $db=config::getConnexion();
        $query=$db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    function Adduser($User){
        $sql = "INSERT INTO User (nom, prenom, email, motdepasse, role, photo)
                VALUES (:nom, :prenom, :email, :motdepasse, :role, :photo)";
        $db=config::getConnexion();

        $query=$db->prepare($sql);
        $query->bindValue('nom',$User->getNom());
        $query->bindValue('prenom',$User->getPrenom());
        $query->bindValue('email',$User->getEmail());

        $hashedPassword = password_hash($User->getMotdepasse(), PASSWORD_DEFAULT);
        $query->bindValue('motdepasse',$hashedPassword);

        $query->bindValue('role', $User->getRole());
        $query->bindValue('photo', $User->getPhoto());
        $query->execute();
    }
function login($email, $motdepasse){
    $sql = "SELECT * FROM User WHERE email = :email";
    $db = config::getConnexion();

    $query = $db->prepare($sql);
    $query->bindValue(':email', $email);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // 🔴 1) Vérifier si le compte est désactivé
        if ($user['status'] === 'inactive') {
            return "inactive"; 
        }

        // 🟢 2) Vérifier le mot de passe
        if (password_verify($motdepasse, $user['motdepasse'])) {
            return $user;
        }
    }

    return false;
}

    public function updateUser($user){
        $db = config::getConnexion();
        $sql = "UPDATE User 
                SET nom = :nom, prenom = :prenom, email = :email, photo = :photo 
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email' => $user->getEmail(),
            ':photo' => $user->getPhoto(),
            ':id' => $user->getId()
        ]);
    }

    public function getUserById($id){
        $db = config::getConnexion();
        $sql = "SELECT * FROM User WHERE id=:id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id'=>$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if($data){
            return new User(
                $data['id'],
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['motdepasse'],
                $data['role'],
                $data['photo']
            );
        }
        return null;
    }

    public function deleteUser($id){
        $sql="DELETE FROM User WHERE id=:id"; 
        $db=config::getConnexion();
        $query=$db->prepare($sql);
        $query->execute([':id'=>$id]);
    }
    public function resetPassword($email, $newPassword)
{
    $db = config::getConnexion();

    // Vérifier si l'utilisateur existe
    $sql = "SELECT * FROM User WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        return "Email introuvable";
    }

    // Hash du mot de passe
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    // Mise à jour
    $updateSql = "UPDATE User SET motdepasse = :motdepasse WHERE email = :email";
    $up = $db->prepare($updateSql);
    $ok = $up->execute([
        ':motdepasse' => $hashed,
        ':email' => $email
    ]);

    return $ok ? "ok" : "Erreur lors de la mise à jour";
}

public function createResetCode($email) {
    $db = config::getConnexion();

    // Vérifier si email existe
    $check = $db->prepare("SELECT * FROM User WHERE email = :email");
    $check->execute([':email' => $email]);
    $user = $check->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false; // email introuvable
    }

    // Générer un code COURT (6 chiffres)
    $code = rand(100000, 999999);

    // Stocker dans la base
    $update = $db->prepare("UPDATE User SET reset_code = :code WHERE email = :email");
    $update->execute([
        ':code' => $code,
        ':email' => $email
    ]);

    return $code; // on retourne le code
}

public function checkResetCode($code) {
    $db = config::getConnexion();

    $stmt = $db->prepare("SELECT * FROM User WHERE reset_code = :code");
    $stmt->execute([':code' => $code]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function updatePasswordFromCode($code, $newPassword) {
    $db = config::getConnexion();

    // Vérifier token
    $user = $this->checkResetCode($code);
    if (!$user) {
        return false;
    }

    // Hash password
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    // Mettre à jour mot de passe + vider reset_code
    $sql = "UPDATE User SET motdepasse = :mdp, reset_code = NULL WHERE reset_code = :code";
    $stmt = $db->prepare($sql);
    $ok = $stmt->execute([
        ':mdp' => $hashed,
        ':code' => $code
    ]);

    return $ok ? true : false;
}

public function desactiverCompte($id) {
    $db = config::getConnexion();
    $sql = "UPDATE User SET status = 'inactive' WHERE id = :id";
    $stmt = $db->prepare($sql);
    return $stmt->execute([':id' => $id]);
}


}

?>


