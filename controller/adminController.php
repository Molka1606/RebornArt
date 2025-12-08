<?php 
require __DIR__. "../../model/config.php";
require __DIR__. "../../model/Utilisateur.php";

class adminController {

    function getAllUser(){
        $sql="SELECT * FROM User";
        $db=config::getConnexion();
        try{
            $query=$db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        }
        catch(Exception $e){
            echo ("erreur ".$e->getMessage());
        }
    }

    function Adduser($User){
        $sql = "INSERT INTO User (id, nom, prenom, email, motdepasse, role)
                VALUES (NULL, :nom, :prenom, :email, :motdepasse, :role)";
        $db=config::getConnexion();
        try{
            $query=$db->prepare($sql);
            $query->bindValue('nom',$User->getNom());
            $query->bindValue('prenom',$User->getPrenom());
            $query->bindValue('email',$User->getEmail());

            $hashedPassword = password_hash($User->getMotdepasse(), PASSWORD_DEFAULT);
            $query->bindValue('motdepasse',$hashedPassword);

            $query->bindValue('role', $User->getRole());
            $query->execute();
        }
        catch(Exception $e){
            echo ("erreur ".$e->getMessage());
        }
    }


function login($email, $motdepasse){
    $sql = "SELECT * FROM User WHERE email = :email";
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->bindValue(':email', $email);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if ($user['status'] === 'inactive') {
                return "inactive";
            }

            if (password_verify($motdepasse, $user['motdepasse'])) {
                return $user;
            }
        }

        return false;

    } catch(Exception $e) {
        echo "Erreur : ".$e->getMessage();
        return false;
    }
}


public function updateProfile($id, $nom, $prenom, $email, $currentPassword, $newPassword = null, $photo = null) {
    $db = config::getConnexion();
    
    // Récupérer l'utilisateur actuel
    $sql = "SELECT * FROM User WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['motdepasse'])) {
        return false; 
    }

    // Si aucune nouvelle photo n'est envoyée → garder l'ancienne de la BDD
    if ($photo === null) {
        $photoToSave = isset($user['photo']) ? $user['photo'] : null;
    } else {
        $photoToSave = $photo;
    }

    // Avec changement de mot de passe
    if ($newPassword && !empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE User 
                SET nom = :nom, prenom = :prenom, email = :email, motdepasse = :motdepasse, photo = :photo 
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':motdepasse' => $hashedPassword,
            ':photo' => $photoToSave,
            ':id' => $id
        ]);
    } else {
        // Sans changement de mot de passe
        $sql = "UPDATE User 
                SET nom = :nom, prenom = :prenom, email = :email, photo = :photo
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':photo' => $photoToSave,
            ':id' => $id
        ]);
    }

    return true;
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
            isset($data['role']) ? $data['role'] : 'user' 
        );
    }
    return null;
}

public function deleteUser($id){
    $sql="DELETE FROM User WHERE id=:id"; 
    $db=config::getConnexion();
    $query=$db->prepare($sql);
    try{
        $query->execute([
            ':id'=>$id, 
        ]);
    }
    catch(Exception $e){
        echo "Erreur : ".$e->getMessage();
    }
}

public function desactiverAdmin($id) {
    $db = config::getConnexion();
    $sql = "UPDATE User SET status = 'inactive' WHERE id = :id";
    $stmt = $db->prepare($sql);
    return $stmt->execute([':id' => $id]);
}

public function updatePassword($email, $newPassword) {
    $db = config::getConnexion();
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE User SET motdepasse = :motdepasse WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':motdepasse', $hashed);
    $stmt->bindValue(':email', $email);

    $stmt->execute();
}


}
?>