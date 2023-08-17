<?php

/**
 * Tests unitaires (avec PHPUnit) sur la classe Personnage (en passant par la classe Magicien)
 */

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// Appel des dépendances nécessaires
require 'Personnage.php';
require 'Magicien.php';

final class PersonnageTest extends TestCase
{
    public static function baseDegatsProvider(): array
    {
        return [
            '0 dégat de base' => [0],
            '50 dégats de base' => [50],
            '99 dégats de base' => [99]
        ];
    }

    #[DataProvider('baseDegatsProvider')]
    public function testPersonnageRecoitDegats(int $base_degats): void
    {
        $perso = new Magicien(['degats' => $base_degats]);

        $resultHit = $perso->recevoirDegats();

        $this->assertSame(Personnage::TARGET_HIT, $resultHit);
    }

    #[DataProvider('baseDegatsProvider')]
    public function testPersonnageMeurt(int $base_degats): void
    {
        $perso = new Magicien(['degats' => $base_degats]);

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
