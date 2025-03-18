<?php
include_once '../fonctions.php';
include_once '../pokemonCard.php';

$db = connectToDB("localhost","pokedex","root","");
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pokemonToRemove = $_POST['pokemonsupp'];
    $pokemon = getPokemonsFromSqlDb($db, $pokemonToRemove)[0];
    $pokemon -> delPokemon($db);
    $messageAlert = "removed";
}

header("Location: ../index.php?alert=" . urlencode($messageAlert) . "&pokemon=" . urlencode($pokemonToRemove));

exit();

?>