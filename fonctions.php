<?php
// Fonctions pour la pokedex

include_once 'pokemonCard.php';

//______________________________________________________________________________//
// Récupérer un Post
function getPostForm($value){
    return isset($_POST[$value]) ? trim($_POST[$value]) : "";
}

//______________________________________________________________________________//
// Connexion à la base de données
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
// Récupérer l'id d'un type, talent ou résistance de pokemon
function getAttributeId($db, $table, $attributeName) {
    try {
        $sql = "SELECT id FROM $table WHERE name = :name";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $attributeName);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    } catch (PDOException $e) {
        echo "<br><br> [$table] Erreur de récupération d'ID d'attribut : " . $e->getMessage();
        return null;
    }
}

//______________________________________________________________________________//
// Insérer des attributs dans la base de données
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
                echo "<br><br> [$table] Erreur d'ajout de valeurs d'attributs : " . $e->getMessage();
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
                echo "<br><br> [$table] Erreur d'ajout de valeurs d'attributs : " . $e->getMessage();
            }
        }
    }
}

//______________________________________________________________________________//
// Insérer des liens entre les tables
function InsertTablesLinks($db, $table, $pokemonID, $attributId){
    try {
        $sql = "INSERT IGNORE INTO $table VALUES(:id1, :id2)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id1", $pokemonID);
        $stmt->bindParam(":id2", $attributId);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "<br><br> [$table] Erreur d'ajout des liens : " . $e->getMessage();
    }
}

//______________________________________________________________________________//
// Définir les liens entre les tables
function setLinks($db, $table, $values, $pokemonId){
    $table_link = $table . "_l";
    foreach($values as $value){
        $attrId = getAttributeId($db, $table, $value);
        InsertTablesLinks($db, $table_link, $pokemonId, $attrId);
    }
}

//______________________________________________________________________________//
// Ajouter un surnom à un Pokémon
function addNickname($db, $pokemonName, $nickname){
    $sql = "UPDATE pokemons SET nickname = :surnom WHERE name = :name";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":surnom", $nickname);
    $stmt->bindParam(":name", $pokemonName);
    $stmt->execute();
}

//______________________________________________________________________________//
// Convertir une ligne SQL en objet PokemonCard
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
        $nickname
    );
}

//______________________________________________________________________________//
// Vérifier si un Pokémon existe déjà
function doesPokemonExists($db, $pokemonName){
    $pokemons = getPokemonsFromSqlDb($db, $pokemonName);
    foreach($pokemons as $pokemon){
        if (strtolower($pokemon->name) == strtolower($pokemonName)){
            return true;
        }
    }
    return false;    
}

//______________________________________________________________________________//
// Récupérer un Pokémon depuis l'API
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

//______________________________________________________________________________//
// Structurer les données d'un Pokémon à partir du JSON
function structPokemonDataFromJson($data){
    if(count($data) <= 5){
        return null;
    }
    $talents = [];
    $resistances = [];
    $types = [];

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
        $weight
    );
}

//______________________________________________________________________________//
// Récupérer les Pokémon depuis la base de données SQL
function getPokemonsFromSqlDb($db, $name = '', $search = '') {
    $pokemons = [];

    $sql = "SELECT p.pokemon_id, p.name, p.category, p.nickname,
                CONCAT(p.image_url, ', ', COALESCE(p.shiny_img, '')) AS images,
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
    
    if ($name != '' && $search == '') {
        $sql .= " WHERE p.name = :name ";
    }if ($search != '' && $name == ''){
        $sql .= " WHERE p.name like '%$search%'";
    }

    $sql .= " GROUP BY p.pokemon_id, p.name, p.category, p.size, p.weight, p.nickname, images
    ORDER BY p.name ASC";

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

//______________________________________________________________________________//
// Supprimer un Pokémon de la base de données
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

//______________________________________________________________________________//
// Fonction pour récupérer les types de Pokémon
function getPokemonsTypes($db) {
    $pokemonsTypes = [];
    $pokemons = getPokemonsFromSqlDb($db);
    
    // Compter le nombre de Pokémon par type
    foreach ($pokemons as $pokemon){
        $types = array_keys($pokemon->types);
        foreach ($types as $type){
            if(!isset($pokemonsTypes[$type])){
                $pokemonsTypes[$type] = 1;
            } else {
                $pokemonsTypes[$type]++;
            }
        }
    }

    // Trier le tableau par valeur (nombre de Pokémon)
    arsort($pokemonsTypes);
    return $pokemonsTypes;
}

//______________________________________________________________________________//
// Fonction pour afficher les boutons des types de Pokémon
function showTypesButtons($db, $typesAndNumbers, $selectedType = '') {
    $types = array_keys($typesAndNumbers);
    $NumberOfPokemons = count(getPokemonsFromSqlDb($db));
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
// Fonction pour afficher les données des Pokémon
function ShowPokemons($pokemons, $type =''){
    echo '<div class="pokedex">';
    foreach($pokemons as $pokemon){
        if($type != ''){
            if(isset($pokemon->types[$type])){
                echo $pokemon->ShowPokemonCard();
            }            
        } else {
            echo $pokemon->ShowPokemonCard();
        }
    }
    echo "</div>";
}

?>