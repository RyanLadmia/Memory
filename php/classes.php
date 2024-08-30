<?php

// Inclu la classe database
include 'php/_connect.php';

class user{ // REPRESENTE UN UTILISATEUR
    private $conn;

    public function __construct();
        // Cree une instance de la classe database et établi la connexion a la BDD
        $database = new Database();
        $this->conn = $database->connect();
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

    public $table();

    public function shuffle(){

    }

    public function drawPairs(){

    }


}


?>