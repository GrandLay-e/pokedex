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
        $sql = "INSERT INTO $table (name, type1, type2, image_url) VALUES ('".$this->name."', '".$this->type1."', '".$this->type2."', '".$this->img_url."')";
        $db->exec($sql);
    }

    //Methode pour afficher les données d'un pokemon
    public function ShowPokemonCard(){
        $Card = "";
        $Card .= "<div class='pokemon-card'>";
        $Card .= "<h2 class = pokename>".$this->name."</h2>";
        $Card .= "<img src='".$this->img_url."' alt='".$this->name."'>";
        $Card .= "<br><p class='type type-".strtolower($this->type1)."'>".$this->type1."</p> ";
        if (!empty($this->type2)) {
            $Card .= "<p class='type type-".strtolower($this->type2)."'>".$this->type2."</p> ";
        }
        $Card .="<form action='index.php' method='POST'>";
        $Card .="<input type='hidden'name='pokemonsupp' value='".$this->name."'>";
        $Card .="<input type='submit' value='Supprimer' id='suppbutton'>";
        $Card .="</form>";
        $Card .= "</div>";

        return $Card;
    }
}

?>