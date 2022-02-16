<?php

// Inclusion du fichier s'occupant de la connexion à la DB
require __DIR__.'/inc/db.php';

// Initialisation de variables
$videogameList = array();
$platformList = array();
$name = '';
$editor = '';
$release_date = '';
$platform = '';

$errorList = [];

// Si le formulaire a été soumis
if (!empty($_POST)) {
    // Récupération des valeurs du formulaire dans des variables
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    $editor = isset($_POST['editor']) ? $_POST['editor'] : '';
    $release_date = isset($_POST['release_date']) ? $_POST['release_date'] : '';

    // intval transforme une chaine de caractère en valeur numérique
    $platform = isset($_POST['platform']) ? intval($_POST['platform']) : 0;
    
    if (empty($name)) {
        $errorList [] = 'Le nom est obligatoire';
    }

    if (empty($editor)) {
        $errorList [] = 'L\'éditeur est obligatoire';
    }

    if (empty($platform)) {
        $errorList [] = 'La plateforme est obligatoire';
    }
    
    // Insertion en DB du jeu video
    $insertQuery = "
        INSERT INTO videogame (name, editor, release_date, platform_id)
        VALUES ('{$name}', '{$editor}', '{$release_date}', {$platform})
    ";

    if (empty($errorList)) {
        // Si je n'ai pas de données vides...alors je fais un insert

        $isInserted = $pdo->exec($insertQuery);
    
        if ($isInserted !== false) {
            // Je fais une redirection propre pour éviter le bug de la double soumission de données        
            // Je repars d'une feuille blanche, sans les données qui ont été soumises avant
            header('Location: index.php');
            exit();
        } else {
            $errorList [] = 'Erreur de sauvegarde';
        }
    }
}

// Liste des consoles de jeux
$requete = $pdo->query('SELECT id, name FROM platform');

$platformList = $requete->fetchAll(PDO::FETCH_KEY_PAIR);

$sql = '
    SELECT * FROM videogame
';

// Si un tri a été demandé, je réécris la requête

$hasOrder = false;
if (!empty($_GET['order'])) {

    // Le tri est en place, je vais pouvoir afficher le bouton "Annuler le trie"
    $hasOrder = true;

    // Récupération du tri choisi
    // La fonction trim supprimer les espaces avant et après une chaine de caractère
    $order = trim($_GET['order']);
    if ($order == 'name') {
        $sql = '
            SELECT * FROM videogame ORDER BY name;
        ';
    }
    else if ($order == 'editor') {
        $sql = '
            SELECT * FROM videogame ORDER BY editor ASC;
        ';
    }
}

// Execution de la requete
$pdoStatement = $pdo->query($sql);

// Récupération du résultat de la requete
$videogameList = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

// Inclusion du fichier s'occupant d'afficher le code HTML
// Je fais cela car mon fichier actuel est déjà assez gros
require __DIR__.'/view/videogame.php';
