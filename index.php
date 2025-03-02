<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <title>Pokedex</title>
    </head>
    <body>
        <h1 class="poketitle" id ="pokedex">P   O   K   E   D   E   X  </h1>
        <a href="#formulaire">
            <button class="goTo">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#75FBFD">
                    <path d="M480-200 240-440l56-56 184 183 184-183 56 56-240 240Zm0-240L240-680l56-56 184 183 184-183 56 56-240 240Z"/>
                </svg>
            </button>
        </a>
            <?php
            // Inclusion des fichiers
            include_once 'pokemonCard.php';
            include_once 'fonctions.php';

            // Variables 
            $csvFile = "pokemons.csv";
            $pokeRemoving = false;
            $pokefound = true;
            $pokeExist = false;
            $messageAlert = '';
            
            // Récupération des données
            $pokemon = trim(getPostForm("pokemon"));
            $pokemonToRemove = getPostForm('pokemonsupp');  
            $selectedType = getPostForm('typeselect');

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                //Gestion de la suppression d'un pokemon si le formulaire est soumis
                if($pokemonToRemove != ''){
                    reoderPokemons($csvFile, $pokemonToRemove);
                    $pokeRemoving = true;
                }
               
                //Gestion de l'ajout d'un pokemon si le formulaire est soumis
                if($pokemon != ''){
                    $PokemonCard = getPokemonFromApi($pokemon);
                    if($PokemonCard->name == ''){
                        $pokefound = false;
                    }else{
                        if(!doesPokemonExists($csvFile, $PokemonCard->name)){
                            $PokemonCard->AddPokemonToCsv($csvFile);
                        }else{
                            $pokeExist = true;
                        }
                    }
                }
            }
            
            // Définir le message d'alerte à envoyer selon les cas
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

            //Phase d'affichage des données
            echo $messageAlert;
            //Récupération les pokemons par ordre alphabétique et aussi supprimer les doublons si y'en a
            reoderPokemons($csvFile);

            //Récupération des types de pokemons
            $types = getPokemonsTypes($csvFile);

            //Récupération des pokemons selon le type sélectionné
            $pokemons = getPokemonsFromCsv($csvFile, $selectedType);

            //Affichage des boutons de types et des pokemons
            showTypesButtons($types, $selectedType);

            //Affichage des pokemons
            ShowPokemons($pokemons);
            
        ?>
        <!-- Formulaire qui sert à ajouter un pokemon -->
        <form action="index.php" method="POST" id="formulaire">
            <input type="text" name="pokemon" placeholder="Pokemon">
            <input type="submit" value="Ajouter">
        </form>

        <!-- Bouton pour remonter en haut de la page -->
        <a href="#pokedex" >
            <button class="goTo">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#75FBFD">
                <path d="m296-224-56-56 240-240 240 240-56 56-184-183-184 183Zm0-240-56-56 240-240 240 240-56 56-184-183-184 183Z"/>
            </svg>
            </button>
        </a>
        
    </body>
</html>
