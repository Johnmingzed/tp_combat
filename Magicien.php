<?php

class Magicien extends Personnage
{
    public function lancerSort(Personnage $cible)
    {
        if ($this->estEndormi()) {
            return self::ASLEEP;
        }
        // La cible à ensorceler n'est pas le magicien qui lance le sort.
        if ($cible->getId() == $this->id) {
            return self::TARGET_ME;
        }
        // Le magicien n'a plus de magie.
        if ($this->getAtout() <= 0) {
            return self::NO_MANA;
        }
        // On endort la cible et on affiche le message
        $time = ($this->getAtout() * 6) * 3600; // C'est beaucoup : 24h de base
        $cible->setTimeEndormi(time() + $time);
        return self::TARGET_SLEEP;
    }
}
