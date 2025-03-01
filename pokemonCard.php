<?php

include_once 'fonctions.php';

class Pokemon_card{
    public $name;
    public $type1;
    public $type2;
    public $img_url;

    public function __construct($name, $type1, $type2, $img_url){
        $this->name = $name;
        $this->type1 = $type1;
        $this->type2 = $type2;
        $this->img_url = $img_url;
    }

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

    public function AddPokemonToCsv($csvFile){
        writeArrayToCsv($csvFile, $this->pokemonCardToArray(), 'a');
    }

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