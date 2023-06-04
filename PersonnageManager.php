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
        $q = $this->db->prepare('INSERT INTO personnages (nom, type) VALUES (:nom, :type)');
        // Assignation des valeurs (nom et type)
        $q->bindValue(':nom', $personnage->getNom());
        $q->bindValue(':type', $personnage->getType());
        // Exécution de la requête
        $q->execute();
        // Hydratation du personnage avec assignation de son ID et paramêtres initiaux (dégats)
        $personnage->hydrate(array(
            'id' => $this->db->lastInsertId(),
            'degats' => 0,
            'atout' => 0
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
            switch ($dataPersonnage['type']) {
                case 'guerrier':
                    return new Guerrier($dataPersonnage);
                case 'magicien':
                    return new Magicien($dataPersonnage);
                default:
                    return null;
            }
        }
    }

    public function update(Personnage $personnage)
    {
        // Préparation de la requête de mise à jour via l'ID du personnage
        $q = $this->db->prepare('UPDATE personnages SET degats = :degats, atout = :atout, timeEndormi = :timeEndormi WHERE id = :id');
        // Assignation des valeurs (degats, ID)
        $q->bindValue(':id', $personnage->getId());
        $q->bindValue(':degats', $personnage->getDegats());
        $q->bindValue(':atout', $personnage->getAtout());
        $q->bindValue(':timeEndromi', $personnage->getTimeEndormi());
        // Exécution de la requête
        $q->execute();
    }

    public function delete(Personnage $personnage)
    {
        // Préparation de la requête d'effacement via l'ID du personnage
        $q = $this->db->prepare('DELETE FROM personnages WHERE id = :id');
        // Assignation de l'ID
        $q->bindValue(':id', $personnage->getId());
        // Exécution de la requête
        $q->execute();
        // Effacement de l'entitée personnage
        unset($personnage);
    }

    public function count()
    {
        // Exécution de la requête de comptage
        $q = $this->db->query('SELECT COUNT(*) FROM personnages');
        // Retourne le nombre de personnages
        return $q->fetchColumn();
    }

    public function getList(?string $nom)
    {
        // Exécution de la requête de sélection des personnages
        if (isset($nom) && is_string($nom)) {
            $q = $this->db->prepare('SELECT * FROM personnages WHERE nom != :nom ORDER BY nom');
            $q->bindValue(':nom', $nom);
            $q->execute();
        } else {
            $q = $this->db->query('SELECT * FROM personnages ORDER BY nom');
        }
        $dataPersonnages = $q->fetchAll(PDO::FETCH_ASSOC);
        // Construction d'un tableau de personnage
        $personnagesList = [];
        foreach ($dataPersonnages as $personnage) {
            switch ($personnage['type']) {
                case 'guerrier':
                    return $personnagesList[] = new Guerrier($personnage);
                case 'magicien':
                    return $personnagesList[] = new Magicien($personnage);
            }
        }
        // Retour de la liste
        return $personnagesList;
    }

    public function exists(int|string $info)
    {
        if (isset($info)) {
            // Si $info est un int, c'est un ID, on prépare une requête COUNT avec WHERE id = $info
            if (is_int($info)) {
                $q = $this->db->prepare('SELECT COUNT(*) FROM personnages WHERE id = :info');
            }
            // Si $info est un string, c'est un nom, on prépare une requête COUNT avec WHERE nom = $info
            if (is_string($info)) {
                $q = $this->db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :info');
            }
            // On exécute la requête et on retourne un bool
            $q->bindValue(':info', $info);
            $q->execute();
            $result = $q->fetchColumn();
            if ($result == 1) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
