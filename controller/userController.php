<?php
require_once __DIR__ . "/../model/config.php";
require_once __DIR__ . "/../model/Utilisateur.php";

class userController
{
    /* ---------------------------------------------------------
        GET ALL USERS
    --------------------------------------------------------- */
    public function getAllUser()
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM User";

        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------------------------
        ADD USER
    --------------------------------------------------------- */
function Adduser($User){
    $sql = "INSERT INTO User (nom, prenom, email, motdepasse, role, photo, status)
            VALUES (:nom, :prenom, :email, :motdepasse, :role, :photo, :status)";

    $db = config::getConnexion();

    $query = $db->prepare($sql);

    $query->bindValue(':nom', $User->getNom());
    $query->bindValue(':prenom', $User->getPrenom());
    $query->bindValue(':email', $User->getEmail());

    $hashedPassword = password_hash($User->getMotdepasse(), PASSWORD_DEFAULT);
    $query->bindValue(':motdepasse', $hashedPassword);

    $query->bindValue(':role', $User->getRole());
    $query->bindValue(':photo', $User->getPhoto());

    // 🔥 ICI → on force le statut à "active"
    $query->bindValue(':status', 'active');

    $query->execute();
}


    /* ---------------------------------------------------------
        LOGIN USER
    --------------------------------------------------------- */
    public function login($email, $motdepasse)
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM User WHERE email = :email LIMIT 1";

        $query = $db->prepare($sql);
        $query->bindValue(':email', $email);
        $query->execute();

        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            // ❌ Account disabled
            if (isset($user['status']) && $user['status'] === 'inactive') {
                return "inactive";
            }

            // ✔ Password correct
            if (password_verify($motdepasse, $user['motdepasse'])) {
                return $user;
            }
        }

        return false;
    }

    /* ---------------------------------------------------------
        UPDATE USER
    --------------------------------------------------------- */
    public function updateUser($user) {
        $sql = "UPDATE User SET 
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    photo = :photo,
                    telephone = :telephone,
                    date_naissance = :date_naissance
                WHERE id = :id";

        $db = config::getConnexion();
        $query = $db->prepare($sql);

        $query->bindValue(':id', $user->getId());
        $query->bindValue(':nom', $user->getNom());
        $query->bindValue(':prenom', $user->getPrenom());
        $query->bindValue(':email', $user->getEmail());
        $query->bindValue(':photo', $user->getPhoto());
        $query->bindValue(':telephone', $user->getTelephone());
        $query->bindValue(':date_naissance', $user->getDateNaissance());

        return $query->execute();
    }


    /* ---------------------------------------------------------
        GET USER BY ID
    --------------------------------------------------------- */
    public function getUserById($id)
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM User WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
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

    /* ---------------------------------------------------------
        DELETE USER
    --------------------------------------------------------- */
    public function deleteUser($id)
    {
        $db = config::getConnexion();
        $sql = "DELETE FROM User WHERE id = :id";

        $query = $db->prepare($sql);
        return $query->execute([':id' => $id]);
    }
public function emailExists($email) {
    $db = config::getConnexion();
    $email = trim($email); // Supprime les espaces
    $sql = "SELECT * FROM User WHERE TRIM(LOWER(email)) = LOWER(:email)";
    $stmt = $db->prepare($sql);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}

    /* ---------------------------------------------------------
        DEACTIVATE ACCOUNT
    --------------------------------------------------------- */
    public function desactiverCompte($id)
    {
        $db = config::getConnexion();
        $sql = "UPDATE User SET status = 'inactive' WHERE id = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
public function updatePassword($email, $newPassword)
{
    $db = config::getConnexion();

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql = "UPDATE User SET motdepasse = :mdp WHERE email = :email";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':mdp', $hashedPassword);
    $stmt->bindValue(':email', $email);

    return $stmt->execute();
}


}

?>
