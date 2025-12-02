<?php 
require __DIR__. "/../model/config.php";
require __DIR__. "/../model/Utilisateur.php";

class userController {

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

            if($user && password_verify($motdepasse, $user['motdepasse'])) {
                return $user;
            } else {
                return false;
            }
        } catch(Exception $e) {
            echo "Erreur : ".$e->getMessage();
            return false;
        }
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

}
?>



