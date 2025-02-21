# Pokedex

## Introduction
Ce projet présente un Pokedex utilisant HTML et PHP. Il permet d'ajouter, de supprimer et d'afficher des pokemons depuis une API externe et un fichier CSV.

## Structure HTML
Le fichier HTML contient :
- Une balise `<head>` avec les métadonnées et le lien vers le fichier CSS.
- Une balise `<body>` qui affiche le titre et les cartes de chaque pokemon.
- Un formulaire permettant d'ajouter des pokemons en entrant leur nom.

## Fonctionnalités PHP
Le code PHP inclut :
- Une classe `Pokemon_card` pour représenter un pokemon avec ses propriétés (`name`, `type1`, `type2`, `img_url`).
- Des fonctions pour lire et écrire les données dans un fichier CSV.
- Une fonction pour récupérer les informations d'un pokemon depuis une API externe.
- Une fonction pour réordonner les pokemons par ordre alphabétique et gérer la suppression.

Le code PHP gère également les requêtes POST pour ajouter ou supprimer des pokemons et met à jour l'affichage en conséquence.
