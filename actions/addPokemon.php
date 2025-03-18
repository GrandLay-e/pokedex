<?php
include_once '../fonctions.php';
include_once '../pokemonCard.php';

$db = connectToDB("localhost","pokedex","root","");
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pokemon = $_POST['pokemon'];
    if($pokemon != ''){
        $pokeCard = getPokemonFromApi( $pokemon);
        // var_dump($pokeCard);
        if($pokeCard != null){
            if(!doesPokemonExists($db, $pokeCard->name)){
                $pokeCard->SavePokemonToSQLDb( $db );
                $messageAlert = "added_successfully";
            }else{
                $messageAlert = "alredy_exists";
            }
        }else{
            $messageAlert = "not-found"; 
        }
    }
}

header("Location: ../index.php?" . ($messageAlert ? "alert=" . urldecode($messageAlert) . "&pokemon= ". urldecode($pokeCard->name) : ""));
exit();

?>