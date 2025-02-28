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

}

?>