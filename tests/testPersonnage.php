<?php

/**
 * Tests unitaires (réalisés à la main) sur la classe Personnage (en passant par la classe Magicien)
 */

// Appel des dépendances nécessaires
require '../Personnage.php';
require '../Magicien.php';

function runTests()
{
    // Test recevoirDegats()
    testRecevoirDegats(0);
    testRecevoirDegats(50);
    testRecevoirDegats(99);
}

function testRecevoirDegats(int $degatsPersonnage)
{
    // Le test concernant une méthode de classe il faut instancier un objet
    $perso = new Magicien(['degats' => $degatsPersonnage]);

    echo "<p>Test de la fonction recevoirDegats() avec une base de $degatsPersonnage dégâts:<br>";

    $resultHit = $perso->recevoirDegats();
    echo "-> Le personnage reçoit des dégâts : ";
    assertEquals(Personnage::TARGET_HIT, $resultHit);

    $resultDeath = $perso->recevoirDegats();
    echo "-> Le personnage meurt : ";
    assertEquals(Personnage::TARGET_DEATH, $resultDeath);
    echo "</p>";
}

// Fonction d'assertion simplifiée
function assertEquals($expected, $actual)
{
    if ($expected === $actual) {
        echo "PASS<br>";
    } else {
        echo "FAIL (Expected: $expected, Actual: $actual)<br>";
    }
}

// Exécution des tests
runTests();
