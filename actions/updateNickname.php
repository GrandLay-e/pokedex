<?php
include_once '../fonctions.php';
include_once '../pokemonCard.php';

$db = connectToDB("localhost","pokedex","root","");
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    $pokemonName = getPostForm('pokemonName');
    $nickname = getPostForm('nickname');
    if($pokemonName != '' && $nickname != ''){
        if(doesPokemonExists($db,$pokemonName)){
            $pokemon = getPokemonsFromSqlDb($db, $pokemonName)[0];
            $pokemon->addNickname($db,$nickname);
        }
    }
}

header("Location: ../index.php");
exit();

?>