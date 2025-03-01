# Pokedex

## Introduction
Ce projet présente un Pokedex utilisant HTML et PHP. Il permet d'ajouter, de supprimer, de modifier et d'afficher des pokemons depuis une API externe et un fichier CSV.

## Structure HTML
Le fichier HTML contient :
- Une balise `<head>` avec les métadonnées et le lien vers le fichier CSS.
- Une balise `<body>` qui affiche le titre et les cartes de chaque pokemon.
- Un formulaire permettant d'ajouter et de modifier des pokemons en entrant leur nom et leurs détails.

## Fonctionnalités PHP
Le code PHP inclut :
- Une classe `Pokemon_card` pour représenter un pokemon avec ses propriétés (`name`, `type1`, `type2`, `img_url`).
- Des fonctions pour lire et écrire les données dans un fichier CSV.
- Une fonction pour récupérer les informations d'un pokemon depuis une API externe.
- Des fonctions pour réordonner les pokemons par ordre alphabétique, gérer la suppression et la modification.

Le code PHP gère également les requêtes POST pour ajouter, supprimer et modifier des pokemons et met à jour l'affichage en conséquence.
