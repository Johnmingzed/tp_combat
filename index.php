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

if (isset($_POST['creer']) && isset($_POST['nom'])) {
    $personnage = new Personnage(['nom' => $_POST['nom']]);

    if (!$personnage->nomValide()) {
        $msg = 'Le nom choisi est invalide';
        unset($personnage);
    } elseif ($manager->exists($personnage->getNom())) {
        $msg = 'Le nom du personnage est déjà pris.';
        unset($personnage);
    } else {
        $manager->add($personnage);
    }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($manager->exists($_POST['nom'])) {
        $personnage = $manager->select($_POST['nom']);
    } else {
        $msg = 'Ce personnage n\'existe pas.';
    }
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
            }
        }
    }
}
?>

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
        <fieldset>
            <legend>Mes informations</legend>
            <p>
                Nom : <?php echo htmlspecialchars($personnage->getNom()); ?><br>
                Dégâts : <?php echo $personnage->getDegats(); ?>
            </p>
        </fieldset>
        <fieldset>
            <legend>Qui attaquer ?</legend>
            <p>
                <?php
                $ennemis = $manager->getList($personnage->getNom());
                if (empty($ennemis)) {
                    echo 'Aucun ennemi à attaquer';
                } else {
                    foreach ($ennemis as $ennemi) {
                        echo '<a href="?attaquer=' . $ennemi->getId() . '">' . htmlspecialchars($ennemi->getNom()) . '</a> (dégâts : ' . $ennemi->getDegats() . ')<br>';
                    }
                }
                ?>
            </p>
        </fieldset>
    <?php else : ?>
        <form action="" method="post">
            <label for="nom">Nom : </label>
            <input type="text" name="nom" id="nom" maxlength="50">
            <input type="submit" value="Créer ce personnage" name="creer">
            <input type="submit" value="Utiliser ce personnage" name="utiliser">
        </form>
    <?php endif ?>

</body>

</html>

<?php
if (isset($personnage)) {
    $_SESSION['personnage'] = $personnage;
}
