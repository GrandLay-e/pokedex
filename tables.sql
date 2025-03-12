CREATE TABLE pokemons (
    pokemon_id INT PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL,
    nickname VARCHAR(50),
    category VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    shiny_img VARCHAR(255) NOT NULL,
    size INT NOT NULL,
    weight INT NOT NULL
    );

CREATE TABLE types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

CREATE TABLE talents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE resistances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE types_l (
    pokemon_id INT NOT NULL,
    type_id INT NOT NULL,
    PRIMARY KEY (pokemon_id, type_id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemons(pokemon_id),
    FOREIGN KEY (type_id) REFERENCES types(id)
);

CREATE TABLE talents_l (
    pokemon_id INT NOT NULL,
    talent_id INT NOT NULL,
    PRIMARY KEY (pokemon_id, talent_id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemons(pokemon_id),
    FOREIGN KEY (talent_id) REFERENCES talents(id)
);

-- Table for linking Pokemons and their resistances
CREATE TABLE resistances_l (
    pokemon_id INT NOT NULL,
    resistance_id INT NOT NULL,
    PRIMARY KEY (pokemon_id, resistance_id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemons(pokemon_id),
    FOREIGN KEY (resistance_id) REFERENCES resistances(id)
);

