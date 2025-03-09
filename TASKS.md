# Reformer la classe PokemonCard

## Attributs
- **Nom**
- **Catégorie**
- **Types** []
- **Liens Images** []
- **Talents** []
- **Résistances** []
- **Taille**
- **Poids**

## Changer le constructeur

## Sauvegarder sur les bases de données

### Pokemons
- **ID Pokemon**
- **Nom**
- **Image Regular**
- **Catégorie**
- **Image Shiny**
- **Taille**
- **Poids**
- **ID Talents**
    - Chaque talent est stocké dans la table `talents`
    - Et le lien est aussi automatiquement fait dans la table des liens (talent, pokemon)
- **ID Résistance**
    - Ajouter des liens pour chaque résistance trouvée

### Types
- **ID**
- **Nom**
- **Lien Image Type**

### Talent
- **ID**
- **Nom**

### Résistance
- **ID**
- **Nom**

### Tables de Liens 
#### Lien Type
- **ID Type** [de la table `types`]
- **ID Pokemon** [de la table `pokemons`]

#### Lien Talent
- **ID Talent** [de la table `talents`]
- **ID Pokemon** [de la table `pokemons`]

#### Lien Résistance
- **ID Résistance** [de la table `resistances`]
- **ID Pokemon** [de la table `pokemons`]

## Ordre de Sauvegarde

1. Sauvegarder les types dans la table `types`.
2. Sauvegarder les talents dans la table `talents`.
3. Sauvegarder les résistances dans la table `resistances`.
4. Sauvegarder la carte Pokémon dans la table `pokemons`.
5. Créer les liens pour les types dans la table `lien type`.
6. Créer les liens pour les talents dans la table `lien talent`.
7. Créer les liens pour les résistances dans la table `lien resistance`.

## Prochaine étape
- *Récupération des données*
    - Gèrer les récupèration
        - Les jointures avec toutes les tables necessaires
        - le formattage pour l'adapter à l'objet PokemonCard

-  *L'affichage des informations*
    - Séparer l'affichage en carte (tous les pokemons sur une seule page)
    - Et l'affichage en entier d'un pokemon (avec lien `en savoir plus`)
        - Afficher sur une page pokemon.php
        - Esthétique de l'affichage à voir avec le CSS
    - Ajouter les boutons de tous les types ainsi que [le nombre de pokemons] avec ce type 
- *Possibilité de modifier les informations d'un pokemon*