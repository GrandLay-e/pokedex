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

            // Connexion à la base de données
            $host = 'localhost'; // Hôte de la base de données
            $dbname = 'pokedex'; // Nom de la base de données
            $username = 'root'; // Nom d'utilisateur pour se connecter
            $password = ''; // Mot de passe de l'utilisateur pour se connecter
            $table = "pokemons"; //la table SQL
            $db = connectToDB($host, $dbname, $username, $password);



            // Variables 
            $pokeRemoving = false;
            $pokefound = true;
            $pokeExist = false;
            $messageAlert = '';
            
            // // Récupération des données
            $pokemon = trim(getPostForm("pokemon"));
            $pokemonToRemove = getPostForm('pokemonsupp');

            $pokemonName = trim(getPostForm('pokemonName'));
            $nickname = trim(getPostForm('nickname'));

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                //Gestion de la suppression d'un pokemon si le formulaire est soumis
                if($pokemonToRemove != ''){
                    removePokemon($db,  $pokemonToRemove);
                    $pokeRemoving = true;
                }

                if($pokemonName != '' && $nickname != ''){
                    addNickname($db, $pokemonName, $nickname);
                }

                if($pokemon != ''){
                    $pokeCard = getPokemonFromApi( $pokemon);
                    if($pokeCard != null){
                        $pokeFound = true;
                        $pokeCard->SavePokemonToSQLDb( $db );
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

            //Récupération des pokemons selon le type sélectionné
            $pokemons = getPokemonsFromSqlDb($db,  );

            //Affichage des pokemons
            ShowPokemons($pokemons);
            
        // 
        // ?>
        <!-- Formulaire qui sert à ajouter un pokemon -->
    <div class="formulaires">
        <div class="AddPokemon">
            <fieldset width = 10%>
                <legend>Ajouter un Pokémon</legend>
                <form action="index.php" method="POST" id="formulaire">
                    <input type="text" name="pokemon" placeholder="Nom du Pokémon" required>
                    <input type="submit" value="Ajouter">
                </form>
            </fieldset>
        </div>

        <div class="AddNickName">
            <fieldset>
                <legend>Ajouter un Surnom</legend>
                <form action="index.php" method="POST">
                    <input type="text" name="pokemonName" placeholder="Nom du Pokémon" required>
                    <input type="text" name="nickname" placeholder="Surnom" required>
                    <input type="submit" value="Ajouter Surnom">
                </form>
            </fieldset>
        </div>
    </div>

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
