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
            class Pokemon_card{
                public $name;
                public $type1;
                public $type2;
                public $img_url;

                public function __construct($name, $type1, $type2, $img_url){
                    $this->name = $name;
                    $this->type1 = $type1;
                    $this->type2 = $type2;
                    $this->img_url = $img_url;
                }
            }
            //Write on csv
            function writeArryOnCsv($csv_file, $data, $mode){
                $file = fopen($csv_file, $mode);
                foreach($data as $line){
                    fputcsv($file, $line);
                }
                fclose($file);
            }

            ///Fonction pour réordonner le contenu du fichier csv avant de l'afficher 
            /// Elle sert aussi pour supprimer un pokemon
            function reoderPokemons($csv_file , $nameToRemove = ''){
                $file = fopen($csv_file, 'r');

                $pokemons = []; #va centenir les données du fichier csv de base
                $pokemonNames = []; #va contenir les noms des pokemons
                $ord_pokemons = []; #va contenir les données des pokemons ordonnées

                //On récupère les données du fichier csv, ainsi que les noms
                while (($data = fgetcsv($file)) !== FALSE)
                {
                    if($nameToRemove == '')
                    {
                        array_push($pokemonNames, $data[0]);
                    }else{
                        if($data[0] != $nameToRemove){
                            array_push($pokemonNames, $data[0]);
                        }
                    }
                    array_push($pokemons, $data);
                }

                #On trie les noms des pokemons
                sort($pokemonNames);

                #sorcharger la liste des données ordonnbées en passant par les noms triés
                // while(count($ord_pokemons) != count($pokemons)){
                    foreach($pokemonNames as $name){
                        foreach($pokemons as $poke){
                            if(strtolower($name) == strtolower($poke[0])){
                                array_push($ord_pokemons, $poke);
                            }
                        }
                    }
                // }
                fclose($file); #fermer le fichier

                //Voucrir de nouveau pour libérer le fichier (écrire des données vides)
                $file = fopen($csv_file, 'w');
                fclose($file);

                //Ecrire les données ordonnées dans le fichier
                writeArryOnCsv($csv_file, $ord_pokemons, 'w');
            }

            ################################################################################################
            ################################################################################################
            ################################################################################################

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $pokemon = $_POST["pokemon"];
                $pokemonToRemove = $_POST['pokemonsupp'];   
                $pokeRemoving = false;
                $pokefound = false;
                $pokeexist = false;
                $messageAlert = '';
                if($pokemonToRemove != ''){
                    reoderPokemons("pokemons.csv", $pokemonToRemove);
                    $pokeRemoving = true;
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://tyradex.vercel.app/api/v1/pokemon/".$pokemon);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                
                $response = curl_exec($ch);
                $data = json_decode($response, true);

                $name = $data["name"]["fr"];
                $img_url = $data["sprites"]["regular"];
                $types = [];
                foreach ($data["types"] as $type) {
                    array_push($types, $type["name"]);
                }
                $type1 = $types[0];
                $type2 = isset($types[1]) ? $types[1] : '';

                $MyPokemon = new Pokemon_card($name, $type1, $type2, $img_url);

                if($name == ''){
                    $pokefound = false;
                }else{
                    $pokefound = true;
                }
                if (($file = fopen("pokemons.csv", "r")) !== FALSE) {
                    while (($poke = fgetcsv($file)) !== FALSE) {
                        if (strtolower($poke[0]) == strtolower($MyPokemon->name)) {
                            $pokeexist = true;
                            break;
                        }
                    }
                    fclose($file);
                }
                if($pokeexist == false && $pokefound == true){
                    writeArryOnCsv("pokemons.csv", [[$MyPokemon->name, 
                                    $MyPokemon->type1, 
                                    $MyPokemon->type2, 
                                    $MyPokemon->img_url]], 'a');
                }
            }
            
            if($pokefound == false){
                $messageAlert = "<h6 class=messag_alert> Ce pokemon n'existe pas </h6>";
            }
            if($pokeexist){
                $messageAlert = "<h6 class=messag_alert> Ce pokemon est dejà ajouté </h6>";
                $pokeexist = false;
            }if($pokeRemoving && $pokemonToRemove != ''){
                $messageAlert = "<h6 class=messag_alert>Pokemon [$pokemonToRemove] supprimé </h6>";
                $pokeRemoving = false;
            }
            echo $messageAlert;
            echo '<div class="pokedex">';
            reoderPokemons("pokemons.csv");
            $file = fopen("pokemons.csv","r");
            while (($data = fgetcsv($file)) !== FALSE)
            {
                echo '<div class="pokemon-card">';
                echo "<h2 class=pokename>$data[0]</h2>";
                echo "<img src='$data[3]' alt='$data[0]'>";
                echo "<br><p class='type type-".strtolower($data[1])."'>$data[1]</p> ";
                if (!empty($data[2])) {
                    echo "<p class='type type-".strtolower($data[2])."'>$data[2]</p> ";
                }
                echo " <br><form action='index.php' method='POST'>";
                echo "<input type='hidden' name='pokemonsupp' value='$data[0]'>";
                echo "<input type='submit' value='Supprimer' id='suppbutton'>";
                echo "</form>";
                echo '</div>';
            }
            fclose($file);
            echo '</div>';
            
        ?>
        <form action="index.php" method="POST">
            <input type="text" name="pokemon" placeholder="Pokemon">
            <input type="submit" value="Ajouter">
        </form>

    </body>
</html>
