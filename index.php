<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <title>Pokedex</title>
    </head>
    <body>
        <h1 class="poketitle" id ="pokedex">P   O   K   E   D   E   X  </h1>
        <a href="#formulaire" >
            <button class="goTo">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#75FBFD">
                    <path d="M480-200 240-440l56-56 184 183 184-183 56 56-240 240Zm0-240L240-680l56-56 184 183 184-183 56 56-240 240Z"/>
                </svg>
            </button>
        </a>
            <?php
            include 'pokemonCard.php';
            include 'fonctions.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $pokemon = $_POST["pokemon"];
                $pokemonToRemove = $_POST['pokemonsupp'];  
                $selectedType = $_POST['typeselect'];
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
            $types = getPokemonsTypes("pokemons.csv");
            showTypesButtons($types);
            reoderPokemons("pokemons.csv");
            $pokemons = getPokemonsFromCsv("pokemons.csv", $selectedType);
            ShowPokemons($pokemons);
            
        ?>
        <form action="index.php" method="POST" id="formulaire">
            <input type="text" name="pokemon" placeholder="Pokemon">
            <input type="submit" value="Ajouter">
        </form>
        <a href="#pokedex" >
            <button class="goTo">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#75FBFD">
                <path d="m296-224-56-56 240-240 240 240-56 56-184-183-184 183Zm0-240-56-56 240-240 240 240-56 56-184-183-184 183Z"/>
            </svg>
            </button>
        </a>
        
    </body>
</html>
