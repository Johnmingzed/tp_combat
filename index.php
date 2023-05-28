<?php
// Appel des infos de configuration
require_once 'config.php';

// Autoloader
function Autoload($classname)
{
    require $classname . '.php';
}
spl_autoload_register('Autoload');

// Instanciation de PDO
try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}

// Instanciation du manager
$manager = new PersonnageManager($db);

if (isset($_POST['create']) && isset($_POST['nom'])) {
    $personnage = new Personnage(['nom' => $_post['nom']]);

    if (!$personnage->nomValide()) {
        $msg = 'Le nom choisi est invalide';
        unset($personnage);
    } elseif ($manager->exists($personnage->getNom())) {
        $msg = 'Le nom du personnage est déjà pris.';
        unset($personnage);
    } else {
        $manager->add($personnage);
    }
} elseif (isset($_POST['use']) && isset($_POST['nom'])) {
    if ($manager->exists($_post['nom'])) {
        $perso = $manager->select($_POST['nom']);
    } else {
        $msg = 'Ce personnage n\'existe pas.';
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
        echo `<p>$msg</p>`;
    }
    ?>
    <form action="" method="post">
        <label for="nom">Nom : </label>
        <input type="text" name="nom" id="nom" maxlength="50">
        <input type="submit" value="Créer ce personnage" name="create">
        <input type="submit" value="Utiliser ce personnage" name="use">
    </form>

</body>

</html>