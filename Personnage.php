<?php

class Personnage
{
    private int $id;
    private int $degats = 0;
    private string $nom;

    const TARGET_ME = 1;
    const TARGET_DEATH = 2;
    const TARGET_HIT = 3;
    const DAMAGE = 5;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function frapper(Personnage $cible)
    {
        // Vérifier qu'on ne se frappe pas soi-même
        if ($cible->getId() == $this->id) {
            return self::TARGET_ME;
        }
        // Transmettre des dégâts au personnage cible
        return $cible->recevoirDegats();
    }

    public function recevoirDegats()
    {
        // On augmente les dégats de 5
        $this->degats += self::DAMAGE;

        // Si on atteind un total de dégâts de 100 le personnage meurt
        if ($this->degats >= 100) {
            return self::TARGET_DEATH;
        }
        // Sinon on confirme la prise de dégâts
        return self::TARGET_HIT;
    }

    public function nomValide()
    {
        if (empty($this->nom)) {
            return false;
        }
        return true;
    }

    /**
     * Get the value of id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of degats
     * @return int
     */
    public function getDegats(): int
    {
        return $this->degats;
    }

    /**
     * Get the value of nom
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Set the value of id
     *
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        if ($id > 0) {
            $this->id = $id;
        }
    }

    /**
     * Set the value of degats
     *
     * @param int $degats
     * @return void
     */
    public function setDegats(int $degats): void
    {
        if ($degats >= 0 && $degats <= 100) {
            $this->degats = $degats;
        }
    }

    /**
     * Set the value of nom
     *
     * @param string $nom
     * @return void
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
}
