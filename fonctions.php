<?php
//Fonctions pour la pokedex

//Ecrire dans le fichier csv
function writeArrayToCsv($csv_file, $data, $mode){
    $file = fopen($csv_file, $mode);
    if ($file !== false) {
        foreach($data as $line){
            fputcsv($file, $line);
        }
        fclose($file);
    } else {
        echo "Erreur lors de l'ouverture du fichier CSV.";
    }
}

function doesPokemonExists($csv_file, $pokemonName){
    $pokemons = getPokemonsFromCsv($csv_file);
    foreach($pokemons as $pokemon){
        if(strtolower($pokemon[0]) == strtolower($pokemonName))
        {
            return true;
        }
    }
    return false;
}

//______________________________________________________________________________//
//Fonction pour récupérer un pokemon depuis l'api 
function getPokemonFromApi($pokemonName){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://tyradex.vercel.app/api/v1/pokemon/".$pokemonName);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    return json_decode($response, true);
}


//______________________________________________________________________________//
//Fonction pour récupérer les données des pokemons depuis le fichier csv
function getPokemonsFromCsv($csv_file, $typeToGet = ''){
    $file = fopen($csv_file, 'r');
    $pokemons = [];
    while (($data = fgetcsv($file)) !== FALSE) {
        if($typeToGet == ''){
            array_push($pokemons, $data);
        }else{
            if(strtolower($data[1]) == strtolower($typeToGet) || strtolower($data[2]) == strtolower($typeToGet)){
                array_push($pokemons, $data);
            }
        }
    }
    fclose($file);
    return $pokemons;
}


//______________________________________________________________________________//
//Fonction pour réordonner le contenu du fichier csv (par ordre alphabétique)avant de l'afficher 
/// Elle sert aussi pour supprimer un pokemon
function reoderPokemons($csv_file, $nameToRemove = '') {
    
    $pokemons = []; #va contenir les données du fichier csv de base
    $pokemonNames = []; #va contenir les noms des pokemons
    $ord_pokemons = []; #va contenir les données des pokemons ordonnées
    
    //On récupère les données du fichier csv, ainsi que les noms
    $file = fopen($csv_file, 'r');
    while (($data = fgetcsv($file)) !== FALSE)
    {
        if($nameToRemove == '')
        {
            array_push($pokemonNames, $data[0]);
        }else{
            if($data[0] != $nameToRemove){
                array_push($pokemonNames, $data[0]);
            }
        }
    }
    fclose($file); #fermer le fichier

    #On récupère les données du fichier csv
    $pokemons = getPokemonsFromCsv($csv_file);

    #On trie les noms des pokemons
    sort($pokemonNames);

    #surcharger la liste des données ordonnées en passant par les noms triés
    foreach($pokemonNames as $name){
        foreach($pokemons as $pokemon){
            if(strtolower($name) == strtolower($pokemon[0])){
                array_push($ord_pokemons, $pokemon);
            }
        }
    }

    //Voucrir de nouveau pour libérer le fichier (écrire des données vides)
    $file = fopen($csv_file, 'w');
    fclose($file);

    //Ecrire les données ordonnées dans le fichier
    writeArrayToCsv($csv_file, $ord_pokemons, 'w');
}
//______________________________________________________________________________//
//Fonction pour récupèrer les types de pokemons
function getPokemonsTypes($csv_file){
    $pokemonsTypes = [];
    $pokemons = getPokemonsFromCsv($csv_file);
    foreach($pokemons as $pokemon){
        if(array_search($pokemon[1], $pokemonsTypes) === false ){
            array_push($pokemonsTypes, $pokemon[1]);
        }
        if ($pokemon[2] != '' && array_search($pokemon[2], $pokemonsTypes) === false){
            array_push($pokemonsTypes, $pokemon[2]);
        }
       }
       sort($pokemonsTypes);
       return $pokemonsTypes;
}

function showTypesButtons($types) {
    echo "<form action='index.php' method='post'>";
    echo "<nav class='types'>";
    echo "<button type='submit' name='typeselect' value='' class='type type-tout'> TOUT </button>";
    foreach ($types as $type) {
        echo "<button type='submit' name='typeselect' value='$type' class='type type-" . strtolower($type) . "'>$type</button>";
    }
    echo "</nav>";
    echo "</form>";
}

//______________________________________________________________________________//
//Fonction pour afficher les données des pokemons
function ShowPokemons($pokemons){
    echo '<div class="pokedex">';
    foreach($pokemons as $pokemon){
        echo "<div class='pokemon-card'>";
        echo "<h2 class = pokename>".$pokemon[0]."</h2>";
        echo "<img src='".$pokemon[3]."' alt='".$pokemon[0]."'>";
        echo "<br><p class='type type-".strtolower($pokemon[1])."'>$pokemon[1]</p> ";
        if (!empty($pokemon[2])) {
            echo "<p class='type type-".strtolower($pokemon[2])."'>$pokemon[2]</p> ";
        }
        echo"<form action='index.php' method='POST'>";
        echo"<input type='hidden'name='pokemonsupp' value='$pokemon[0]'>";
        echo"<input type='submit' value='Supprimer' id='suppbutton'>";
        echo"</form>";
        echo "</div>";
    }
    echo "</div>";
}
?>