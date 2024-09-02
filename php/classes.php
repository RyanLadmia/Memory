<?php

// Inclu la classe database
include __DIR__ . '/../includes/_connect.php';

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function register($login, $password) {
        if (empty($login) || empty($password)) {
            return "Le login et le mot de passe ne peuvent pas être vides.";
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

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

                    return "Connexion réussie.";
                } else {
                    error_log("Tentative de connexion échouée pour le login: " . htmlspecialchars($login) . " - Mot de passe incorrect.");
                    return "Mot de passe incorrect.";
                }
            } else {
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

    public function __construct($src) {
        $this->src = $src;
    }

    public function getSrc() {
        return $this->src;
    }
}

class MemoryGame {
    private $cards = [];
    private $flipped = [];
    private $foundPairs = [];
    private $attempts = 0;

    public function __construct($cardSources, $sets) {
        $this->initializeGame($cardSources, $sets);
    }
    
    private function initializeGame($cardSources, $sets) {
        $cardSources = array_slice($cardSources, 0, $sets); // Limite le nombre de paires
        $this->cards = $this->createCards($cardSources);
    }

    private function createCards($cardSources) {
        $cardList = [];
        foreach ($cardSources as $src) {
            $cardList[] = new Card($src['src']);
        }
        $cards = array_merge($cardList, $cardList); // Crée des paires
        shuffle($cards); // Mélange les cartes
        return $cards;
    }

    public function getCards() {
        return $this->cards;
    }

    public function flipCard($index) {
        if (in_array($index, $this->foundPairs)) {
            return;
        }

        $this->flipped[] = $index;

        if (count($this->flipped) == 2) {
            $this->attempts++;
            $firstCard = $this->cards[$this->flipped[0]];
            $secondCard = $this->cards[$this->flipped[1]];

            if ($firstCard->getSrc() === $secondCard->getSrc()) {
                $this->foundPairs[] = $this->flipped[0];
                $this->foundPairs[] = $this->flipped[1];
            }

            $this->flipped = [];
        }
    }
    
    public function shuffleCards() {
        shuffle($this->cards);
        $this->flipped = [];
        $this->foundPairs = [];
        $this->attempts = 0;
    }

    public function getFlipped() {
        return $this->flipped;
    }

    public function getFoundPairs() {
        return $this->foundPairs;
    }

    public function getAttempts() {
        return $this->attempts;
    }
    
    public function isGameOver() {
        return count($this->foundPairs) == count($this->cards);
    }
    
    public function getEndMessage() {
        if ($this->isGameOver()) {
            return "Félicitations. Vous avez remporté la partie en " . $this->attempts . " tentatives!";
        }
        return "";
    }
}

class MemoryGameController {
    private $game;
    private $message;

    public function __construct($cardSources) {
        session_start();

        if (isset($_POST['new_game'])) {
            $sets = $_POST['sets'] ?? 3;
            $sets = (int)str_replace('_sets', '', $sets);
            $this->game = new MemoryGame($cardSources, $sets);
            $_SESSION['memory_game'] = $this->game;
        } elseif (isset($_POST['shake'])) {
            if (isset($_SESSION['memory_game'])) {
                $this->game = $_SESSION['memory_game'];
                $this->game->shuffleCards();
                $_SESSION['memory_game'] = $this->game;
            }
        } else {
            $this->game = isset($_SESSION['memory_game']) ? $_SESSION['memory_game'] : new MemoryGame($cardSources, 3);
        }

        $this->message = $this->game->getEndMessage();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
            $index = (int)$_POST['index'];
            $this->game->flipCard($index);
            $_SESSION['memory_game'] = $this->game;
            $this->message = $this->game->getEndMessage();
        }
    }

    public function getGame() {
        return $this->game;
    }

    public function getMessage() {
        return $this->message;
    }
}
