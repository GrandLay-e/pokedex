<?php

include_once 'fonctions.php';

//______________________________________________________________________________//
//Classe Pokemon_card
class Pokemon_card{
    public $name;
    public $type1;
    public $type2;
    public $img_url;

    //Constructeur de la classe
    public function __construct($name, $type1, $type2, $img_url){
        $this->name = $name;
        $this->type1 = $type1;
        $this->type2 = $type2;
        $this->img_url = $img_url;
    }

    //Methode pour convertir les données d'un pokemon en tableau
    public function pokemonCardToArray(){
        return [
            [
            $this->name,
            $this->type1,
            $this->type2,
            $this->img_url
            ]
        ];
    }

    //methode pour ajouter un pokemon dans la base de donnée SQL
    public function AddPokemonToSQL($db, $table){
        try {
            $sql = "INSERT INTO $table (name, type1, type2, image_url) VALUES (:name, :type1, :type2, :img_url)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':type1', $this->type1);
            $stmt->bindParam(':type2', $this->type2);
            $stmt->bindParam(':img_url', $this->img_url);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    //Methode pour afficher les données d'un pokemon
    public function ShowPokemonCard(){
        $Card = "";
        $Card .= "<div class='pokemon-card'>";
        $Card .="<form action='index.php' method='POST'>";
        $Card .="<input type='hidden'name='pokemonsupp' value='".$this->name."'>";
        $Card .= "<button type='submit' value='Supprimer' id='suppbutton'>";
        $Card .= "<svg xmlns='http://www.w3.org/2000/svg' 
        // height='24px' viewBox='0 -960 960 960' width='24px' fill='#FFFFFF'>
        // <path d='m336-280-56-56 144-144-144-143 56-56 144 144 143-144 56 56-144 143 144 144-56 56-143-144-144 144Z'/>
        // </svg>";
        $Card .= "</button>";
        $Card .="</form>";
        
        
        $Card .= "<h2 class = pokename>".$this->name."</h2>";
        $Card .= "<img src='".$this->img_url."' alt='".$this->name."'>";
        $Card .= "<br><p class='type type-".strtolower($this->type1)."'>".$this->type1."</p> ";
        if (!empty($this->type2)) {
            $Card .= "<p class='type type-".strtolower($this->type2)."'>".$this->type2."</p> ";
        }


        // $Card .="<form action='index.php' method='POST'>";
        // $Card .="<input type='hidden'name='pokemonsupp' value='".$this->name."'>";
        // $Card .="<input type='submit' value='Supprimer' id='suppbutton'>";
        $Card .="</form>";
        $Card .= "</div>";

        return $Card;
    }
}

?>