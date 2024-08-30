<?php
// CONNEXION A LA BASE DE DONNEE
class Database {
    private $db_server = "localhost";
    private $db_name = "memory";
    private $db_user = "root";
    private $db_password = "";
    private $conn;

    public function connect() {
        try {
            // Correction des noms de variables
            $this->conn = new PDO("mysql:host=" . $this->db_server . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die("La connexion à la base de données a échoué : " . $e->getMessage());
        }
    }
}

?>
