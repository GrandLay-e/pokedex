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

function addNickname($db, $pokemonName, $nickname){
    $sql = "UPDATE pokemons SET nickname = :surnom Where name = :name";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":surnom", $nickname);
    $stmt->bindParam(":name", $pokemonName);
    $stmt->execute();
}
function sqlRowToPokemonCard($row){
    
    $forematedtype = [];
    $pokemon_id = $row['pokemon_id'];
    $name = $row['name'];
    $category = $row['category'];
    $images = explode(', ', $row['images']);
    $formatedImages = [
        'regular' => $images[0],
        'shiny' => $images[1]
    ];
    $types = explode(',', $row['types']);
    foreach($types as $type){
        $temp = explode('~', $type);
        $forematedtype[trim($temp[0])] = trim($temp[1]);
    }
    $talents = explode(', ', $row['talents']);
    $resistances = explode(', ', $row['resistances']);
    $size = $row['size'];
    $weight = $row['weight'];
    $nickname = $row['nickname'];

    return new PokemonCard(
        $pokemon_id, 
        $name, 
        $category, 
        $forematedtype, 
        $formatedImages, 
        $talents, 
        $resistances, 
        $size, 
        $weight,
        $nickname);
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

    return structPokemonDataFromJson($data);
}


function structPokemonDataFromJson($data){
    // var_dump($data);
    $talents = [];
    $resistances = [];
    $types = [];
    // $images = [];

    $pokemon_id = $data["pokedex_id"];
    $name = $data["name"]["fr"];
    $category = $data["category"];
    $types = [$data["types"][0]["name"] => $data["types"][0]["image"]];
    if(isset($data["types"][1])){
        $types[$data["types"][1]["name"]] = $data["types"][1]["image"];
    }
    $images = [
        'regular' => $data["sprites"]["regular"],
        'shiny' => $data["sprites"]["shiny"]
    ];

    foreach($data['talents'] as $talent){
        $talents[] = $talent["name"];
    }
    $resistances = [];
    foreach($data['resistances'] as $resistance){
        $resistances[] = $resistance["name"];
    }
    $size = $data["height"];
    $weight = $data["weight"];

    return new PokemonCard(
        $pokemon_id, 
        $name, 
        $category, 
        $types, 
        $images, 
        $talents, 
        $resistances, 
        $size, 
        $weight);
}

function getPokemonsFromSqlDb($db, $name = '') {
    $pokemons = [];

    $sql = "SELECT p.pokemon_id, p.name, p.category, p.nickname,
                CONCAT(p.image_url, ', ', p.shiny_img) AS images,
                GROUP_CONCAT(DISTINCT CONCAT(ty.name, '~', ty.image_url) ORDER BY ty.name ASC SEPARATOR ', ') AS types,
                GROUP_CONCAT(DISTINCT ta.name ORDER BY ta.name ASC SEPARATOR ', ') AS talents,
                GROUP_CONCAT(DISTINCT r.name ORDER BY r.name ASC SEPARATOR ', ') AS resistances,
                p.size, p.weight
            FROM pokemons p
            INNER JOIN resistances_l rl ON rl.pokemon_id = p.pokemon_id
            INNER JOIN resistances r ON r.id = rl.resistance_id
            INNER JOIN talents_l tal ON tal.pokemon_id = p.pokemon_id
            INNER JOIN talents ta ON ta.id = tal.talent_id
            INNER JOIN types_l tyl ON tyl.pokemon_id = p.pokemon_id
            INNER JOIN types ty ON ty.id = tyl.type_id";
    
    if ($name != '') {
        $sql .= " WHERE p.name = :name ";
    }

    $sql .= " GROUP BY p.pokemon_id, p.name, p.category, p.size, p.weight, p.nickname, images";

    $stmt = $db->prepare($sql);

    if ($name != '') {
        $stmt->bindParam(':name', $name);
    }

    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pokemons[] = sqlRowToPokemonCard($row);
    }

    return $pokemons;
}


function removePokemon($db, $nameToRemove) {
    try {
        // Démarrer une transaction
        $db->beginTransaction();

        // Préparer la requête pour obtenir l'ID du Pokémon
        $stmt = $db->prepare("SELECT pokemon_id FROM pokemons WHERE name = :nameToRemove");
        $stmt->bindParam(':nameToRemove', $nameToRemove);
        $stmt->execute();
        
        // Récupérer l'ID du Pokémon
        $pokemonId = $stmt->fetchColumn();

        if ($pokemonId) {
            // Supprimer les associations dans les tables de liaison
            $stmt = $db->prepare("DELETE FROM types_l WHERE pokemon_id = :pokemonId");
            $stmt->bindParam(':pokemonId', $pokemonId);
            $stmt->execute();

            $stmt = $db->prepare("DELETE FROM talents_l WHERE pokemon_id = :pokemonId");
            $stmt->bindParam(':pokemonId', $pokemonId);
            $stmt->execute();

            $stmt = $db->prepare("DELETE FROM resistances_l WHERE pokemon_id = :pokemonId");
            $stmt->bindParam(':pokemonId', $pokemonId);
            $stmt->execute();

            // Supprimer le Pokémon
            $stmt = $db->prepare("DELETE FROM pokemons WHERE name = :nameToRemove");
            $stmt->bindParam(':nameToRemove', $nameToRemove);
            $stmt->execute();
        }

        // Valider la transaction
        $db->commit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollBack();
        echo "Erreur lors de la suppression du Pokémon : " . $e->getMessage();
    }
}


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
//Fonction pour afficher les données des pokemons
function ShowPokemons($pokemons){
    echo '<div class="pokedex">';
    foreach($pokemons as $pokemon){
        echo $pokemon->ShowPokemonCard();
    }
    echo "</div>";
}

function showPokemonDetails($pokemon){
    $details = "<div class='pokemon-details'>";
    $details .= "<h2 class='pokemon-name'>" . $pokemon->name . "</h2>";
    $details .= "" . $pokemon->category . "<br>";
    $details .= "<div class='line1'> <img class='imagepk' src='" . $pokemon->img_urls['regular'] . "' alt='Image regular de " . $pokemon->name . "'>";
    
    $details .= "<div class='inside'>";

    $details .= "<div> Size : " . $pokemon->size . " <br> Weight : " . $pokemon->weight . " </div>";
    
    $details .= "<div class='types_p'>";
    foreach($pokemon->types as $type => $img){
        $details .= "<div id = onetype>";
        $details .= "<p class='type-" .strtolower($type) . "'>" . $type . "</p>";
        $details .= " <img class = 'type-img' src='" . $img . "' alt='Image de " . $type . "'>";
        $details .= "</div>";
    }
    $details .= "</div>";

    $details .= "</div>";
    
    $details .= "<img class='imagepk' src='" . $pokemon->img_urls['shiny'] . "' alt='Image shiny de " . $pokemon->name . "'>";
    $details .= "</div>";

    $details .= "<h3> Talents </h3>";
    $details .= "<div class='talents-resistances'>";
    foreach($pokemon->talents as $talent){
        $details .= "<div class='talent'>" . $talent . "</div>";
    }
    $details .= "</div>";

    $details .= "<h3> Resistances </h3>";
    $details .= "<div class='talents-resistances'>";
    foreach($pokemon->resistances as $resistance){
        $details .= "<div class='resistance'>" . $resistance . "</div>";
    }
    $details .= "</div>";
    $details .= "</div>";

    return $details;
}
?>