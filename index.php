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
        <div class="recherche">
            <form action="index.php" method="POST" class ="cherche">
                <input type="text" name="searchpokemon" class="searchbar">
                <input type="submit" value="S E A R C H" id = "search_button">
            </form>
        </div>
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

            // type selectionné
            $selectedType = getPostForm('typeselect');
            $cherche = getPostForm('searchpokemon');
    
            $pokemon = $_GET['pokemon'] ?? '';
            $alert = $_GET['alert'] ?? ''; 
            $messageAlert = [
                "added_successfully" => "<h6 class='message_alert succes_added'>$pokemon ajouté avec succès !</h6>",
                "alredy_exists" => "<h6 class='message_alert'>$pokemon est déjà ajouté</h6>",
                "not-found" => "<h6 class='message_alert'>Ce Pokémon n'existe pas</h6>",
                "removed" => "<h6 class='message_alert'>$pokemon est supprimé</h6>"
            ];
            
            //Phase d'affichage des données
            if($alert != ''){
                echo $messageAlert[$alert];
            }

            //Récupération des pokemons selon le type sélectionné
            $pokemons = getPokemonsFromSqlDb($db, '' ,$cherche);

            //récupération et affichage des types
            $types = getPokemonsTypes( $db );
            showTypesButtons($db, $types, $selectedType);

            //Affichage des pokemons
            ShowPokemons($pokemons, $selectedType);
            
        ?>
        <!-- Formulaire qui sert à ajouter un pokemon -->
    <div class="formulaires">
        <div class="AddPokemon">
            <fieldset width = 10%>
                <legend>Ajouter un Pokémon</legend>
                <form action="actions/addPokemon.php" method="POST" id="formulaire">
                    <input type="text" name="pokemon" placeholder="Nom du Pokémon" required>
                    <input type="submit" value="Ajouter">
                </form>
            </fieldset>
        </div>

        <!-- Formulaire pour ajouter un surnom à un pokemon -->
        <div class="AddNickName">
            <fieldset>
                <legend>Ajouter un Surnom</legend>
                <form action="actions/updateNickname.php" method="POST">
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
