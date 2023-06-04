<?php
// Appel des infos de configuration
require_once 'config.php';
require_once 'vendor/autoload.php';

// Autoloader
function classloader($classname)
{
    require $classname . '.php';
}
spl_autoload_register('classloader');

// Lancement de la session
session_start();
if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

// Instanciation de PDO
try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}

// Instanciation du manager
$manager = new PersonnageManager($db);

// Récupération du personnage en session
if (isset($_SESSION['personnage'])) {
    $personnage = $_SESSION['personnage'];
}

// Création d'un nouveau personnage
if (isset($_POST['creer']) && isset($_POST['nom'])) {
    switch ($_POST['type']) {
        case 'guerrier':
            $personnage = new Guerrier(['nom' => $_POST['nom']]);
            break;
        case 'magicien':
            $personnage = new Magicien(['nom' => $_POST['nom']]);
            break;
        default:
            $msg = 'Le type du personnage est invalide.';
            break;
    }

    if (isset($personnage)) {
        if (!$personnage->nomValide()) {
            $msg = 'Le nom choisi est invalide';
            unset($personnage);
        } elseif ($manager->exists($personnage->getNom())) {
            $msg = 'Le nom du personnage est déjà pris.';
            unset($personnage);
        } else {
            $manager->add($personnage);
        }
    }

    // Utilisation d'un personnage existant
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($manager->exists($_POST['nom'])) {
        $personnage = $manager->select($_POST['nom']);
    } else {
        $msg = 'Ce personnage n\'existe pas.';
    }

    // Attaquer un adversaire
} elseif (isset($_GET['attaquer'])) {
    if (!isset($personnage)) {
        $msg = 'Vous devez créer ou utiliser un personnage existant avant d\'attaquer.';
    } else {
        if (!$manager->exists((int) $_GET['attaquer'])) {
            $msg = 'L\'ennemi que vous voulez attaquer n\'existe pas !';
        } else {
            $cible = $manager->select((int) $_GET['attaquer']);
            $retour = $personnage->frapper($cible);
            switch ($retour) {
                case Personnage::TARGET_ME:
                    $msg = 'Même si vous n\'êtes pas fier de vous ce n\'est vraiment pas une raison pour vous frapper...';
                    break;
                case Personnage::TARGET_HIT:
                    $msg = 'Vous avez touché ' . $cible->getNom() . ' !';
                    $manager->update($personnage);
                    $manager->update($cible);
                    break;
                case Personnage::TARGET_DEATH:
                    $msg = 'Vous avez mis ' . $cible->getNom() . ' KO !';
                    $manager->update($personnage);
                    $manager->delete($cible);
                    break;
                case Personnage::ASLEEP:
                    $msg = 'Vous dormez profondemment et ne pouvez rien faire...';
                    break;
            }
        }
    }

    // Endormir un adversaire
} elseif (isset($_GET['endormir'])) {
    if (!isset($personnage)) {
        $msg = 'Vous devez créer ou utiliser un personnage existant avant d\'agir.';
    } else {
        if (!$manager->exists((int) $_GET['endormir'])) {
            $msg = 'L\'ennemi que vous voulez ensorceler n\'existe pas !';
        } else {
            $cible = $manager->select((int) $_GET['endormir']);
            $retour = $personnage->lancerSort($cible);
            switch ($retour) {
                case Personnage::TARGET_ME:
                    $msg = 'La fatigue est grande mais ce n\'est pas le moment de vous endormir...';
                    break;
                case Personnage::TARGET_SLEEP:
                    $msg = 'Vous avez endormi ' . $cible->getNom() . ' !';
                    $manager->update($personnage);
                    $manager->update($cible);
                    break;
                case Personnage::NO_MANA:
                    $msg = 'Vous n\'avez plus de magie !';
                    break;
                case Personnage::ASLEEP:
                    $msg = 'Vous dormez profondemment et ne pouvez rien faire...';
                    break;
            }
        }
    }
} elseif (isset($_GET['reveiller'])) {
    if (!isset($personnage)) {
        $msg = 'Vous devez créer ou utiliser un personnage existant avant de lever un sort.';
    } else {
        if (!$manager->exists((int) $_GET['reveiller'])) {
            $msg = 'L\'ennemi que vous voulez ensorceler n\'existe pas !';
        } else {
            $cible = $manager->select((int) $_GET['reveiller']);
            $cible->setTimeEndormi(0);
            $manager->update($personnage);
            $manager->update($cible);
            $msg = 'Sortilège de sommeil levé sur ' . $cible->getNom();
        }
    }
}
?>



<!-- AFFICHAGE DE LA VUE -->

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP : Mini jeu de combat</title>
</head>

<body>
    <p>Nombre de personnages créés : <?php echo $manager->count(); ?></p>
    <?php
    if (isset($msg)) {
        echo '<p>' . $msg . '</p>';
    }
    if (isset($personnage)) : ?>
        <p><a href="?deconnexion=1">Déconnexion</a></p>
        <fieldset id="personnage_infos">
            <legend>Mes informations</legend>
            <?php if ($personnage->estEndormi()) {
                echo '<p>Vous êtes endormi !<br> Réveil dans : ';
                echo date("H:i:s", $personnage->reveilTime()) . ' heures</p>';
            }
            ?>
            <p>
                Nom : <?= $personnage->estEndormi() ? '💤 ' : '', htmlspecialchars($personnage->getNom()) ?><br>
                Type : <?php echo htmlspecialchars(ucfirst($personnage->getType())); ?><br>
                Dégâts : <?php echo $personnage->getDegats(); ?><br>
                <?php
                switch ($personnage->getType()) {
                    case 'magicien':
                        echo 'Magie : ';
                        break;
                    case 'guerrier':
                        echo 'Protection : ';
                        break;
                }
                echo $personnage->getAtout();
                ?>
            </p>
        </fieldset>
        <fieldset id="menu_attaque">
            <legend>Attaquer</legend>
            <p>
                <?php
                $ennemis = $manager->getList($personnage->getNom());
                if (empty($ennemis)) {
                    echo 'Aucun ennemi à attaquer';
                } else {
                    foreach ($ennemis as $ennemi) {
                        if ($ennemi->estEndormi()) {
                            echo '💤 ';
                        }
                        echo '<a href="?attaquer=' . $ennemi->getId() . '">' . htmlspecialchars($ennemi->getNom()) . '</a> (' . ucfirst($ennemi->getType()) . ' - dégâts : ' . $ennemi->getDegats() . ')<br>';
                    }
                }
                ?>
            </p>
        </fieldset>
        <?php if ($personnage->getType() == 'magicien') : ?>
            <fieldset id="menu_sort">
                <legend>Lancer un sort</legend>
                <p>
                    <?php
                    $ennemis = $manager->getList($personnage->getNom());
                    if (empty($ennemis)) {
                        echo 'Aucun ennemi à ensorceler';
                    } else {
                        foreach ($ennemis as $ennemi) {
                            echo htmlspecialchars($ennemi->getNom()) . ' (' . ucfirst($ennemi->getType()) . ' - dégâts : ' . $ennemi->getDegats() . ') <a href="?endormir=' . $ennemi->getId() . '">Endormir</a> - <a href="?reveiller=' . $ennemi->getId() . '">Réveiller</a><br>';
                        }
                    }
                    ?>
                </p>
            </fieldset>
        <?php endif ?>

    <?php else : ?>
        <form action="" method="post">
            <label for="nom">Nom : </label>
            <input type="text" name="nom" id="nom" maxlength="50">
            <input type="submit" value="Utiliser ce personnage" name="utiliser"><br>
            <input type="radio" name="type" id="guerrier" value="guerrier">
            <label for="guerrier">Guerrier</label>
            <input type="radio" name="type" id="magicien" value="magicien">
            <label for="magicien">Magicien</label><br>
            </select>
            <input type="submit" value="Créer ce personnage" name="creer">
        </form>
    <?php endif ?>

</body>

</html>

<?php
if (isset($personnage)) {
    $_SESSION['personnage'] = $personnage;
}
