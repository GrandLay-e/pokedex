<?php

include 'fonctions.php';
include_once 'pokemonCard.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $pokemonToShow = getPostForm('pokemonDetails');
}
if($pokemonToShow == ''){
    header('Location: index.php');
    exit();
}

echo"
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='pokemon.css'>
    <link rel='stylesheet' href='style.css'>
    <title>$pokemonToShow</title>
</head>
<body>
";

// Connexion à la base de données
$host = 'localhost'; // Hôte de la base de données
$dbname = 'pokedex'; // Nom de la base de données
$username = 'root'; // Nom d'utilisateur pour se connecter
$password = ''; // Mot de passe de l'utilisateur pour se connecter
$table = "pokemons"; //la table SQL
$db = connectToDB($host, $dbname, $username, $password);

$pokemons = getPokemonsFromSqlDb($db, $pokemonToShow);
$pokemon = $pokemons[0];
echo "<a href='index.php'> << Revenir vers la page d'accueil </a>";
echo showPokemonDetails($pokemon);

echo"
</body>
</html>";

?>