<?php

include_once 'fonctions.php';

//______________________________________________________________________________//
//Classe Pokemon_card
class PokemonCard{
    public $id;
    public $name;
    public $category;
    public $types = []; 
    public $img_urls = [];
    public $talents = []; 
    public $resistances = [];
    public $size; 
    public $weight;
    public $nickname;

    // Constructeur de la classe
    public function __construct(
        $id, 
        $name, 
        $category, 
        array $types = [], 
        array $img_urls = [], 
        array $talents = [], 
        array $resistances = [], 
        $size = 0, 
        $weight = 0,
        $nickname = '') {
            
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->types = $types;
        $this->img_urls = $img_urls;
        $this->talents = $talents;
        $this->resistances = $resistances;
        $this->size = $size;
        $this->weight = $weight;
        $this->nickname = $nickname;
    }

    //methode pour ajouter un pokemon dans la base de donnée SQL

    public function SavePokemonToSQLDb($db){
        
        insertAttribute($db,'types',  array_keys($this->types));
        insertAttribute($db, 'talents', $this->talents);
        insertAttribute($db,'resistances', $this->resistances);
        
        try {
            $sql = "INSERT INTO pokemons (pokemon_id, name, category, image_url, shiny_img, size, weight) 
            VALUES (:id, :name, :category, :image_url, :shiny_img, :size, :weight)
            ON DUPLICATE KEY UPDATE name = name";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':image_url', $this->img_urls['regular']);
            $stmt->bindParam(':shiny_img', $this->img_urls['shiny']);
            $stmt->bindParam(':size', $this->size);
            $stmt->bindParam(':weight', $this->weight);
            $stmt->execute();
        } catch(PDOException $e) {
            echo " <br><br> Error ajout dans la table pokemons: " . $e->getMessage();
        }

        setLinks($db, 'types', array_keys($this->types), $this->id);
        setLinks($db, 'talents', $this->talents, $this->id);
        setLinks($db, 'resistances', $this->resistances, $this->id);
    }

    //Methode pour afficher les données d'un pokemon
    public function ShowPokemonCard(){

        $Card = "<div class='pokemon-card'>
        <form action='index.php' method='POST'>
        <input type='hidden'name='pokemonsupp' value='".$this->name."'>
        <button type='submit' value='Supprimer' id='suppbutton'>
        <svg xmlns='http://www.w3.org/2000/svg' 
        // height='24px' viewBox='0 -960 960 960' width='24px' fill='#FFFFFF'>
        // <path d='m336-280-56-56 144-144-144-143 56-56 144 144 143-144 56 56-144 143 144 144-56 56-143-144-144 144Z'/>
        // </svg>
        </button>
        </form>
        <h2 class = pokename>".$this->name."</h2>";
        
        if($this->nickname != ''){
            $Card .= "<p class = 'nickname' color = 'white'>".$this->nickname."</p>";
        }else{
            $Card .= "<p> <br> </p>";
        }
        
        $Card .= "<img src='".$this->img_urls['regular']."' alt='".$this->name."'>";
        $Card .= "<br><p class='type type-".strtolower(array_keys($this->types)[0])."'>".array_keys($this->types)[0]."</p> ";
        if (!empty(array_keys($this->types)[1])) {
            $Card .= "<p class='type type-".strtolower(array_keys($this->types)[1])."'>".array_keys($this->types)[1]."</p> ";
        }
        $Card .= "<form action='pokemon.php' method='POST'>
       <input type='hidden' name='pokemonDetails' value='".$this->name."'>
       <button type='submit' value='Details' id='detailsbutton'>

        <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#FFFFFF'>
        // <path d='M383-480 200-664l56-56 240 240-240 240-56-56 183-184Zm264 0L464-664l56-56 240 240-240 240-56-56 183-184Z'/>
        // </svg>
        
        </button>
        </form>
        </div>";

        return $Card;
    }
}

?>