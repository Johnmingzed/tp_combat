<?php

class PersonnageManager
{
    private $db; // Instance PDO

    public function __construct($database)
    {
        $this->setDb($database);
    }

    public function setDb(PDO $database)
    {
        $this->db = $database;
    }

    public function add(Personnage $personnage)
    {
        // Préparation de la requête d'insertion
        $q = $this->db->prepare('INSERT INTO personnages SET nom = :nom');
        // Assignation des valeurs (nom)
        $q->bindValue(':nom', $personnage->getNom());
        // Exécution de la requête
        $q->execute();
        // Hydratation du personnage avec assignation de son ID et paramêtres initiaux (dégats)
        $personnage->hydrate(array(
            'id' => $this->db->lastInsertId(),
            'degats' => 0
        ));
    }

    public function select($info)
    {
        // Si $info est un int, on veut récupérer le personnage via son ID.
        if (is_int($info)) {
            // Exécute une requête de type SELECT avec WHERE id=$info, et retourne un objet Personnage.
            $q = $this->db->prepare('SELECT * FROM personnages WHERE id = :info');
        }
        // Si $info est un string, on veut récupérer le personnage avec son nom.
        if (is_string($info)) {
            // Exécute une requête de type SELECT avec WHERE nom=$info, et retourne un objet Personnage.
            $q = $this->db->prepare('SELECT * FROM personnages WHERE nom = :info');
        }
        if (isset($q)) {
            $q->bindValue(':info', $info);
            $q->execute();
            $dataPersonnage = $q->fetch(PDO::FETCH_ASSOC);
            return new Personnage($dataPersonnage);
        }
    }

    public function update(Personnage $personnage)
    {
        // Préparation de la requête de mise à jour via l'ID du personnage
        // Assignation des valeurs (degats, nom)
        // Exécution de la requête
        // Hydratation de personnage avec les nouvelles valeurs
    }

    public function delete(Personnage $personnage)
    {
        // Préparation de la requête d'effacement via l'ID du personnage
        // Assignation de l'ID
        // Exécution de la requête
        // Effacement de l'entitée personnage
    }

    public function count()
    {
        // Exécution de la requête de comptage
        // Retour du nombre de personnages
    }

    public function list()
    {
        // Exécution de la requête de sélection des personnages
        // Construction d'un tableau de personnage
        // Retour de la liste
    }

    public function exists(int|string $info)
    {
        // Si $info est un int, c'est un ID
        // On exécute une requête COUNT() avec WHERE id=$info et on retourne un bool
        // Si $info est un string, c'est un nom
        // On exécute une requête COUNT() avec WHERE nom=$info et on retourne un bool
    }
}
