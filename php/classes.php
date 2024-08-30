<?php

// Inclu la classe database
include '../includes/_connect.php';

class user{ // REPRESENTE UN UTILISATEUR
    private $conn;

    public function __construct(){
        // Cree une instance de la classe database et établi la connexion a la BDD
        $database = new Database();
        $this->conn = $database->connect();
    }

        public function register($login, $password) {
            if (empty($login) || empty($password)) {
                return "Le login et le mot de passe ne peuvent pas être vides.";
            }
        
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
            // Vérifier si le login existe déjà
            $sqlCheck = "SELECT COUNT(*) FROM users WHERE login = :login";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->bindParam(':login', $login);
            $stmtCheck->execute();
            if ($stmtCheck->fetchColumn() > 0) {
                return "Ce login est déjà utilisé.";
            }
        
            $sql = "INSERT INTO users (login, password) VALUES (:login, :password)";
            
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':password', $hashedPassword);
                
                if ($stmt->execute()) {
                    return "Inscription réussie. Vous pouvez maintenant vous connecter.";
                } else {
                    // Log error details for debugging
                    error_log("Failed to execute query: " . implode(" | ", $stmt->errorInfo()));
                    return "Échec de l'inscription. Veuillez réessayer plus tard.";
                }
            } catch (PDOException $e) {
                return "Erreur : " . $e->getMessage();
            }
        }



        public function login($login, $password) {
            if (empty($login) || empty($password)) {
                return "Le login et le mot de passe sont obligatoires.";
            }
        
            $sql = "SELECT id, login, password FROM users WHERE login = :login";
            
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':login', $login);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['userid'] = $user['id'];
                        $_SESSION['login'] = $user['login'];
        
                        $this->login = $user['login']; 
        
                        return "Connexion réussie.";
                    } else {
                        // Optionally log the failed login attempt for security monitoring
                        error_log("Tentative de connexion échouée pour le login: " . htmlspecialchars($login) . " - Mot de passe incorrect.");
                        return "Mot de passe incorrect.";
                    }
                } else {
                    // Optionally log the failed login attempt for security monitoring
                    error_log("Tentative de connexion échouée pour le login: " . htmlspecialchars($login) . " - Utilisateur non trouvé.");
                    return "Utilisateur non trouvé.";
                }
            } catch (PDOException $e) {
                return "Erreur : " . $e->getMessage();
            }
        }

        

        public function update($login, $password) {
            if (!isset($_SESSION['login'])) {
                return "Erreur : Vous devez être connecté pour mettre à jour vos informations.";
            }
        
            if (empty($login) || empty($password)) {
                return "Le login et le mot de passe sont obligatoires.";
            }
        
            // Check if the new login is already taken
            $checkSql = "SELECT COUNT(*) FROM users WHERE login = :login AND login != :currentLogin";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bindParam(':login', $login);
            $checkStmt->bindParam(':currentLogin', $_SESSION['login']);
            $checkStmt->execute();
            if ($checkStmt->fetchColumn() > 0) {
                return "Ce login est déjà utilisé.";
            }
        
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
            $sql = "UPDATE users SET login = :login, password = :password WHERE login = :currentLogin";
        
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':currentLogin', $_SESSION['login']);
        
                if ($stmt->execute()) {
                    $_SESSION['login'] = $login;
        
                    return "Informations mises à jour avec succès.";
                } else {
                    return "Échec de la mise à jour.";
                }
            } catch (PDOException $e) {
                return "Erreur : " . $e->getMessage();
            }
        }



        public function delete() {
            if (!isset($_SESSION['login'])) {
                return "Erreur : Vous devez être connecté pour supprimer votre compte.";
            }
        
            $sql = "DELETE FROM users WHERE login = :login";
            
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':login', $_SESSION['login']);
                
                if ($stmt->execute()) {
                    session_destroy();
                    return "Compte supprimé avec succès.";
                } else {
                    return "Échec de la suppression du compte.";
                }
            } catch (PDOException $e) {
                return "Erreur : " . $e->getMessage();
            }
        }



        public function disconnect() {
            session_destroy();
            $this->login = null;
            
        }
        
}

class card{ // REPRESENTE UNE CARTE DU JEU

    private $id;
    public $Value;
    public $IsVisible;
    public $IsMatched;

    public function flip(){

    }


    public function match(){

    }

}


class deck{ // REPRESENTE LE PAQUET DE CARTE DU JEU

    

    public function shuffle(){

    }

    public function drawPairs(){

    }


}


?>