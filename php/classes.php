<?php

// Inclu la classe database
include __DIR__ . '/../includes/_connect.php';

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

class Card {
    private $src;
    private $id;

    public function __construct($src) {
        $this->src = $src;
        $this->id = uniqid();
    }

    public function getSrc() {
        return $this->src;
    }

    public function getId() {
        return $this->id;
    }
}



class MemoryGame {
    private $cards = [];
    private $flipped = [];
    private $foundPairs = [];
    private $attempts = 0;

    public function __construct($cardSources) {
        $this->initializeGame($cardSources);
    }

    private function initializeGame($cardSources) {
        $this->cards = $this->mix($cardSources);
    }

    private function mix($cardSources) {
        $cardList = [];
        foreach ($cardSources as $src) {
            $cardList[] = new Card($src['src']);
        }
        $shuffledCards = array_merge($cardList, $cardList); 
        shuffle($shuffledCards);
        return $shuffledCards;
    }

    public function getCards() {
        return $this->cards;
    }

    public function flipCard($index) {
        if (!in_array($index, $this->flipped)) {
            $this->flipped[] = $index;
        }

        if (count($this->flipped) == 2) {
            $this->attempts++;
            $firstCard = $this->cards[$this->flipped[0]];
            $secondCard = $this->cards[$this->flipped[1]];

            if ($firstCard->getSrc() === $secondCard->getSrc()) {
                $this->foundPairs[] = $this->flipped[0];
                $this->foundPairs[] = $this->flipped[1];
            }

            // Réinitialiser les cartes retournées
            $this->flipped = [];
        }
    }

    public function getAttempts() {
        return $this->attempts;
    }

    public function getFoundPairs() {
        return $this->foundPairs;
    }
}


class MemoryGameController {
    private $game;

    public function __construct($cardSources) {
        session_start();
        if (!isset($_SESSION['memory_game'])) {
            $this->game = new MemoryGame($cardSources);
            $_SESSION['memory_game'] = $this->game;
        } else {
            $this->game = $_SESSION['memory_game'];
        }
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['index'])) {
            $this->game->flipCard((int)$_POST['index']);
        }
    }

    public function getGame() {
        return $this->game;
    }
}



?>