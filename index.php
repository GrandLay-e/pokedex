<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <title>Pokedex</title>
    </head>
    <body>
        <h1 class="poketitle">P   O   K   E   D   E   X  </h1>
        <?php
            include 'pokemonCard.php';
            include 'fonctions.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $pokemon = $_POST["pokemon"];
                $pokemonToRemove = $_POST['pokemonsupp'];   
                $pokeRemoving = false;
                $pokefound = true;
                $pokeExist = false;
                $messageAlert = '';
                if($pokemonToRemove != ''){
                    reoderPokemons("pokemons.csv", $pokemonToRemove);
                    $pokeRemoving = true;
                }
               
                $data = getPokemonFromApi($pokemon);

                $PokemonCard =  new Pokemon_card($data["name"]["fr"],
                                                $data["types"][0]["name"],
                                                isset($data["types"][1]) ? $data["types"][1]["name"] : '',
                                                $data["sprites"]["regular"]);
                
                if($PokemonCard->name == ''){
                    $pokefound = false;
                }else{
                    $PokemonToAdd = $PokemonCard->pokemonCardToArray();
                    $pokeExist = doesPokemonExists("pokemons.csv", $PokemonToAdd[0][0]);
                    if(!$pokeExist){
                        writeArrayToCsv("pokemons.csv", $PokemonToAdd, 'a');
                    }
                }
            }
            
            if($pokefound == false && $pokemon != ''){
                $messageAlert = "<h6 class=messag_alert> Ce pokemon n'existe pas </h6>";
            }
            if($pokeExist){
                $messageAlert = "<h6 class=messag_alert> Ce pokemon est dejà ajouté </h6>";
                $pokeExist = false;
            }
            if($pokeRemoving && $pokemonToRemove != ''){
                $messageAlert = "<h6 class=messag_alert>Pokemon [$pokemonToRemove] supprimé </h6>";
                $pokeRemoving = false;
            }
            
            echo $messageAlert;
            reoderPokemons("pokemons.csv");
            $pokemons = getPokemonsFromCsv("pokemons.csv");
            ShowPokemons($pokemons);
            
        ?>
        <form action="index.php" method="POST">
            <input type="text" name="pokemon" placeholder="Pokemon">
            <input type="submit" value="Ajouter">
        </form>

    </body>
</html>
