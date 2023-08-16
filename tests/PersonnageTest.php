<?php

/**
 * Tests unitaires (avec PHPUnit) sur la classe Personnage (en passant par la classe Magicien)
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PersonnageTest extends TestCase
{
    public function testPersonnageRecoitDegats(): void
    {
        $perso = new Magicien(['degats' => 0]);

        $resultHit = $perso->recevoirDegats();

        $this->assertSame(Personnage::TARGET_HIT, $resultHit);
    }

    public function testPersonnageMeurt(): void
    {
        $perso = new Magicien(['degats' => 99]);

        $resultDeath = $perso->recevoirDegats();

        $this->assertSame(Personnage::TARGET_DEATH, $resultDeath);
    }
}

/**
 * Pour lancer les tests il faut exécuter la commande
 * ./vendor/bin/phpunit --testdox tests
 * depuis la racine du projet.
 * 
 * PHPUnit détecte et execute automatiquement le contenu des fichiers
 * du dossier "tests" dont les noms terminent par *Test.php
 */
