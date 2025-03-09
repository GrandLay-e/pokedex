<?php
//Fonctions pour la pokedex

include_once 'pokemonCard.php';

//______________________________________________________________________________//
//Récupèrer un Post
function getPostForm($value){
    return isset($_POST[$value]) ? $_POST[$value] : "";
}

//______________________________________________________________________________//
//Connexion à la base de données
function connectToDB($host, $dbname, $username, $password){
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo "<br><br>Erreur de connexion : " . $e->getMessage();
        return null;
    }
}

//______________________________________________________________________________//
//Récupèrer l'id d'un type, talent ou résistance de pokemon
function getAttributeId($db, $table, $attributeName) {
    try {
        $sql = "SELECT id FROM $table WHERE name = :name";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $attributeName);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // echo "<br> ". $result['id'] . "<br>";
        return $result ? $result['id'] : null;
    } catch (PDOException $e) {
        echo "<br><br> [$table] Error dé récupèration d'ID d'attribut : " . $e->getMessage();
        return null;
    }
}


function insertAttribute($db, $table, $values){
    if ($table == 'types') {
        foreach ($values as $key => $value) {
            try {
            $sql = "INSERT INTO $table (name, image_url) VALUES (:name, :image_url) ON DUPLICATE KEY UPDATE name = name";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":name", $key);
            $stmt->bindParam(":image_url", $value);
            $stmt->execute();
            } catch (PDOException $e) {
            echo "<br><br> [$table] Error Ajout de valeurs d'attributs : " . $e->getMessage();
            }
        }
    } else {
        foreach ($values as $value) {
            try {
                $sql = "INSERT INTO $table (name) VALUES (:value) ON DUPLICATE KEY UPDATE name = name";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":value", $value);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "<br><br> [$table] Error Ajout de valeurs d'attributs : " . $e->getMessage();
            }
        }
    }
}
function InsertTablesLinks($db, $table, $pokemonID, $attributId, ){
    try{
        $sql = "INSERT IGNORE INTO $table VALUES(:id1, :id2)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id1", $pokemonID);
        $stmt->bindParam(":id2", $attributId);
        $stmt->execute();
    } catch (PDOException $e){
        echo "<br><br> [$table] Error ajout des liens : ". $e->getMessage();
    }
}

function setLinks( $db, $table, $values, $pokemonId){
    $table_link = $table . "_l";
    foreach($values as $value){
        $attrId = getAttributeId($db, $table, $value);
        InsertTablesLinks( $db, $table_link, $pokemonId, $attrId);
    }
}
// ______________________________________________________________________________//
// Vérifier si OUI ou NON un pokemon a dejà été ajouté
// function doesPokemonExists($db, $table, $pokemonName){
//     $pokemons = getPokemonsFromSqlDb($db, $table);
//     foreach($pokemons as $pokemon){
//         if (strtolower($pokemon->name) == strtolower($pokemonName))
//         {
//             return true;
//         }
//     }
//     return false;    
// }

// // //______________________________________________________________________________//
// //Fonction pour récupérer un pokemon depuis l'api 
// function getPokemonFromApi($pokemonName) {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, "https://tyradex.vercel.app/api/v1/pokemon/" . urlencode($pokemonName));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//     $response = curl_exec($ch);
//     curl_close($ch);
//     return json_decode($response, true);
// }


// function decodeApiResponse($response){
//    echo $response;
// }

// function getPokemonsFromSqlDb($db, $table, $typeToGet =''){
//     $pokemons = [];
//     $sql = "SELECT * FROM $table ";
//     if ($typeToGet != '') {
//         $sql .= " WHERE type1 = :typeToGet OR type2 = :typeToGet ";
//     }
//     $sql .= " ORDER BY name ";
//     $stmt = $db->prepare($sql);
//     if ($typeToGet != '') {
//         $stmt->bindParam(':typeToGet', $typeToGet);
//     }
//     $stmt->execute();
//     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//         array_push($pokemons, new Pokemon_card($row['name'], $row['type1'], $row['type2'], $row['image_url']));
//     }
//     return $pokemons;
// }

// function removePokemon($db, $table, $nameToRemove){
//     $sql = "DELETE FROM $table WHERE name = :nameToRemove";
//     $stmt = $db->prepare($sql);
//     $stmt->bindParam(':nameToRemove', $nameToRemove);
//     $stmt->execute();
// }

// //______________________________________________________________________________//
// //Fonction pour récupèrer les types de pokemons
// function getPokemonsTypes($db, $table) {
//     $pokemonsTypes = [];
//     $pokemons = getPokemonsFromSqlDb($db, $table);

//     // Compter le nombre de pokemons par type
//     foreach ($pokemons as $pokemon) {
//         //Vérifier le premier type
//         if (!empty($pokemon->type1)) {
//             if (!isset($pokemonsTypes[$pokemon->type1])) {
//                 $pokemonsTypes[$pokemon->type1] = 1; 
//             } else {
//                 $pokemonsTypes[$pokemon->type1] += 1; 
//             }
//         }

//         //Vérifier le deuxième type
//         if (!empty($pokemon->type2)) {
//             if (!isset($pokemonsTypes[$pokemon->type2])) {
//                 $pokemonsTypes[$pokemon->type2] = 1; 
//             } else {
//                 $pokemonsTypes[$pokemon->type2] += 1; 
//             }
//         }
//     }

//     // Trier le tableau par clé (type de Pokémon)
//     // ksort($pokemonsTypes);

//     // Trier le tableau par valeur (nombre de Pokémon)
//     arsort($pokemonsTypes);

//     return $pokemonsTypes;
// }

// // //______________________________________________________________________________//
// // //Fonction pour afficher les boutons des types de pokemons
// function showTypesButtons($db, $table, $typesAndNumbers, $selectedType = '') {
//     $types = array_keys($typesAndNumbers);
//     $NumberOfPokemons = count(getPokemonsFromSqlDb($db, $table));
//     $id = '';
//     echo "<form action='index.php' method='POST'>";
//     echo "<nav class='types'>";
//     echo "<button type='submit' name='typeselect' value='' class='type type-tout' id ='tout'> TOUT [ ".$NumberOfPokemons." ] </button>";
//     foreach ($types as $type) {
//         if($type == $selectedType){
//             $id = "selectedType";
//         }
//         echo "<button type='submit' name='typeselect' value='$type' class='type type-" . strtolower($type) . "' id='$id'>".$type." [ ". $typesAndNumbers[$type] ." ] </button>";
//         $id="";
//     }
//     echo "</nav>";
//     echo "</form>";
// }

// // //______________________________________________________________________________//
// //Fonction pour afficher les données des pokemons
// function ShowPokemons($pokemons){
//     echo '<div class="pokedex">';
//     foreach($pokemons as $pokemon){
//         echo $pokemon->ShowPokemonCard();
//     }
//     echo "</div>";
// }

?>