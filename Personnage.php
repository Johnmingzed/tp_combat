<?php

abstract class Personnage
{
    protected int $id;
    protected int $degats = 0;
    protected string $nom;
    protected int $timeEndormi = 0;
    protected string $type;
    protected int $atout = 0;

    const DAMAGE = 5;
    const TARGET_ME = 1;
    const TARGET_DEATH = 2;
    const TARGET_HIT = 3;
    const TARGET_SLEEP = 4;
    const NO_MANA = 5;
    const ASLEEP = 6;

    public function __construct(array $data)
    {
        $this->type = strtolower(get_class($this));
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
        if ($this->estEndormi()) {
            return self::ASLEEP;
        }
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

    public function estEndormi(): bool
    {
        return $this->reveilTime() >= 0 ? true : false;
    }

    public function reveilTime(): int
    {
        $reveil = $this->getTimeEndormi() - time();
        return $reveil;
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
     * Get the value of timeEndormi
     *
     * @return int
     */
    public function getTimeEndormi(): int
    {
        return $this->timeEndormi;
    }

    /**
     * Get the value of atout
     *
     * @return int
     */
    public function getAtout(): int
    {
        return $this->atout;
    }

    /**
     * Get the value of atout
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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

    /**
     * Set the value of timeEndormi
     *
     * @param int $time
     *
     * @return void
     */
    public function setTimeEndormi(int $time): void
    {
        if ($time >= 0) {
            $this->timeEndormi = $time;
        }
    }


    /**
     * Set the value of atout
     *
     * @param int $atout
     *
     * @return void
     */
    public function setAtout(): void
    {
        $degats = $this->getDegats();
        switch (true) {
            case $degats <= 25:
                $atout = 4;
                break;
            case $degats <= 50:
                $atout = 3;
                break;
            case $degats <= 75:
                $atout = 2;
                break;
            case $degats <= 90:
                $atout = 1;
                break;
            default:
                $atout = 0;
                break;
        }
        $this->atout = $atout;
    }
}
