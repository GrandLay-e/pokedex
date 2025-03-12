# Pokedex

## Introduction
Ce projet présente un Pokedex utilisant HTML, PHP et SQL. Il permet d'ajouter, de supprimer, de modifier et d'afficher des pokemons depuis une API externe et de les stocker dans une base de données SQL.

## Structure HTML
Le fichier HTML contient :
- Une balise `<head>` avec les métadonnées et le lien vers le fichier CSS.
- Une balise `<body>` qui affiche le titre et les cartes de chaque pokemon.
- Un formulaire permettant d'ajouter et de modifier des pokemons en entrant leur nom et leurs détails.

## Fonctionnalités PHP
- Une classe `Pokemon_card` pour représenter un pokemon avec ses propriétés :
    - `public $id;`
    - `public $name;`
    - `public $category;`
    - `public $types = [];`
    - `public $img_urls = [];`
    - `public $talents = [];`
    - `public $resistances = [];`
    - `public $size;`
    - `public $weight;`
    - `public $nickname;`
- Des fonctions pour lire et écrire les données dans une base de données SQL.
- Une fonction pour récupérer les informations d'un pokemon depuis l'API Tyradex.
- Des fonctions pour réordonner les pokemons par ordre alphabétique, gérer la suppression et la modification.

Le code PHP gère également les requêtes POST pour ajouter, supprimer et modifier des pokemons et met à jour l'affichage en conséquence.

## Base de données SQL
Le fichier `tables.sql` contient les instructions pour créer les tables nécessaires dans la base de données SQL. Les pokemons récupérés depuis l'API Tyradex sont stockés dans cette base de données avec leurs différentes informations.
