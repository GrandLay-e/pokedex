<?php
//Fonctions pour la pokedex

include_once 'pokemonCard.php';

//______________________________________________________________________________//
//Récupèrer un Post
function getPostForm($value){
    return isset($_POST[$value]) ? $_POST[$value] : "";
}

//______________________________________________________________________________//
//Vérifier si OUI ou NON un pokemon a dejà été ajouté
function doesPokemonExists($csv_file, $pokemonName){
    $pokemons = getPokemonsFromCsv($csv_file);
    foreach($pokemons as $pokemon){
        if (strtolower($pokemon->name) == strtolower($pokemonName))
        {
            return true;
        }
    }
    return false;
    
}

//______________________________________________________________________________//
//Fonction pour récupérer un pokemon depuis l'api 
function getPokemonFromApi($pokemonName) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://tyradex.vercel.app/api/v1/pokemon/" . urlencode($pokemonName));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data)) {
        return new Pokemon_card($data["name"]["fr"], $data["types"][0]["name"],
            isset($data["types"][1]) ? $data["types"][1]["name"] : '',
            $data["sprites"]["regular"]);
    }
    return null;
}



//______________________________________________________________________________//
//Fonction pour récupérer les données des pokemons depuis le fichier csv
function getPokemonsFromCsv($csv_file, $typeToGet = ''){
    $file = fopen($csv_file, 'r');
    $pokemons = [];
    while (($data = fgetcsv($file, 0, ',', '"', '\\')) !== FALSE) {
        if($typeToGet == ''){
            array_push($pokemons, new Pokemon_card($data[0], $data[1], $data[2], $data[3]));
        }else{
            if(strtolower($data[1]) == strtolower($typeToGet) || strtolower($data[2]) == strtolower($typeToGet)){
                array_push($pokemons, new Pokemon_card($data[0], $data[1], $data[2], $data[3]));
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
    // $nameExiste = false;

    #On récupère les données du fichier csv
    $pokemons = getPokemonsFromCsv($csv_file);
    foreach($pokemons as $pokemon){
        if($pokemon->name == $nameToRemove || in_array($pokemon->name, $pokemonNames))
            continue;
        array_push($pokemonNames, $pokemon->name);
    }

    #On trie les noms des pokemons
    sort($pokemonNames);

    #surcharger la liste des données ordonnées en passant par les noms triés
    foreach($pokemonNames as $name){
        foreach($pokemons as $pokemon){
            $nameExist = false;
            foreach($ord_pokemons as $ord_pokemon){
                if($ord_pokemon->name == $name)
                    $nameExist = true;
            }if($pokemon->name == $name && !$nameExist){
                array_push($ord_pokemons, $pokemon);
            }
        }
    }
    

    //ouvrir de nouveau pour libérer le fichier (écrire des données vides)
    $file = fopen($csv_file, 'w');
    fclose($file);

    //Ecrire les données ordonnées dans le fichier
    foreach($ord_pokemons as $pokemon){
        $pokemon->AddPokemonToCsv($csv_file);
    }
}

//______________________________________________________________________________//
//Fonction pour récupèrer les types de pokemons
function getPokemonsTypes($csv_file) {
    $pokemonsTypes = [];
    $pokemons = getPokemonsFromCsv($csv_file);

    foreach ($pokemons as $pokemon) {
        if (!empty($pokemon->type1)) {
            if (!isset($pokemonsTypes[$pokemon->type1])) {
                $pokemonsTypes[$pokemon->type1] = 1; 
            } else {
                $pokemonsTypes[$pokemon->type1] += 1; 
            }
        }

        if (!empty($pokemon->type2)) {
            if (!isset($pokemonsTypes[$pokemon->type2])) {
                $pokemonsTypes[$pokemon->type2] = 1; 
            } else {
                $pokemonsTypes[$pokemon->type2] += 1; 
            }
        }
    }

    // Trier le tableau par clé (type de Pokémon)
    // ksort($pokemonsTypes);

    // Trier le tableau par valeur (nombre de Pokémon)
    arsort($pokemonsTypes);
    
    return $pokemonsTypes;
}

//______________________________________________________________________________//
//Fonction pour afficher les boutons des types de pokemons
function showTypesButtons($typesAndNumbers, $selectedType = '') {
    $NumberOfPokemons = count(getPokemonsFromCsv("pokemons.csv"));
    $types = array_keys($typesAndNumbers);
    $id = '';
    echo "<form action='index.php' method='POST'>";
    echo "<nav class='types'>";
    echo "<button type='submit' name='typeselect' value='' class='type type-tout' id ='tout'> TOUT [ ".$NumberOfPokemons." ] </button>";
    foreach ($types as $type) {
        if($type == $selectedType){
            $id = "selectedType";
        }
        echo "<button type='submit' name='typeselect' value='$type' class='type type-" . strtolower($type) . "' id='$id'>".$type." [ ". $typesAndNumbers[$type] ." ] </button>";
        $id="";
    }
    echo "</nav>";
    echo "</form>";
}

//______________________________________________________________________________//
//Fonction pour afficher les données des pokemons
function ShowPokemons($pokemons){
    echo '<div class="pokedex">';
    foreach($pokemons as $pokemon){
        echo $pokemon->ShowPokemonCard();
    }
    echo "</div>";
}

?>