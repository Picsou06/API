API pour une futur application de coach sportif

Base de donnée:
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    token UUID NOT NULL,
    nom VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
    prenom VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
    naissance DATE NOT NULL,
    taille INT(11) NOT NULL,
    poids FLOAT NOT NULL,
    sexe INT(11) NOT NULL,
    password VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    nb_seance_max INT(11) NOT NULL,
    duree_seance_max INT(11) NOT NULL,
    max_weight FLOAT NOT NULL
);

La requete doit partir vers /api/getprompt et à comme argument
objectif=(perte de poids, musculation...)
niveau=(Debutant, Intermediaire, avancé)
exercices=Liste des exercices disponible (Machine / poids)

Dans le header: 
key: Authorization
Value: Bearer Token_de_ton_user_dans_la_db
