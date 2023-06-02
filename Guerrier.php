<?php

class Guerrier extends Personnage
{
    public function recevoirDegats()
    {
        // Récupération de l'atout
        $bouclier = $this->getAtout();

        // On augmente les dégats supérieur à l'atout
        $this->degats += (self::DAMAGE - $bouclier);

        // Si on atteind un total de dégâts de 100 le personnage meurt
        if ($this->degats >= 100) {
            return self::TARGET_DEATH;
        }
        // Sinon on confirme la prise de dégâts
        return self::TARGET_HIT;
    }
}
