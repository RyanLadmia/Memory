<?php
require_once 'php/classes.php';

$cardSources = [
    ["src" => 'assets/medias/geralt.webp'],
    ["src" => 'assets/medias/yennefer.webp'],
    ["src" => 'assets/medias/ciri.webp'],
    ["src" => 'assets/medias/triss.jpeg'],
    ["src" => 'assets/medias/veasemir.webp'],
    ["src" => 'assets/medias/lambert.jpeg'],
    ["src" => 'assets/medias/eskel.webp'],
    ["src" => 'assets/medias/eredin.webp'],
    ["src" => 'assets/medias/zoltan.webp'],
    ["src" => 'assets/medias/philippa.webp'],
    ["src" => 'assets/medias/jaskier.webp'],
    ["src" => 'assets/medias/cerys.webp']
];

// Verso de mes cartes : 'assets/card_verso.png'

// Initialiser le contrôleur du jeu
$controller = new MemoryGameController($cardSources);

// Gérer la requête
$controller->handleRequest();

// Récupérer le jeu pour l'affichage
$game = $controller->getGame();
$mixCards = $game->getCards();
$message = $controller->getMessage();
?>

<!DOCTYPE html>
<html lang="fr">

    <?php include './includes/_head.php'; ?>

<body class="accueil">

    <?php include './includes/_header.php'; ?>

    <main>
        <h2>Bienvenue sur Memory!</h2>
        <section>
        <form class="ind1" method="post" action="./index.php">
            <label>Nouvelle partie :</label>
            <select name="sets">
                <option value="">Choisissez un nombre de paires</option>
                <option value="3_sets">3 paires</option>
                <option value="4_sets">4 paires</option>
                <option value="5_sets">5 paires</option>
                <option value="6_sets">6 paires</option>
                <option value="7_sets">7 paires</option>
                <option value="8_sets">8 paires</option>
                <option value="9_sets">9 paires</option>
                <option value="10_sets">10 paires</option>
                <option value="11_sets">11 paires</option>
                <option value="12_sets">12 paires</option>
            </select>
            <input type="submit" name="new_game" value="Lancer la partie">
        </form><br>

        <form class="ind2" method="post">
            <input type="submit" name="shake" value="Mélanger les cartes">
        </form><br>
        
        <form class="ind3" method="post">
            <div class="Jeux">
                <?php foreach ($game->getCards() as $index => $card): ?>
                    <?php
                    $isFlipped = in_array($index, $game->getFlipped()) || in_array($index, $game->getFoundPairs());
                    $imgSrc = $isFlipped ? $card->getSrc() : './assets/medias/card_verso.png';
                    ?>
                    <button type="submit" name="index" value="<?php echo $index; ?>" class="card-button">
                        <img src="<?php echo $imgSrc; ?>" alt="Carte">
                    </button>
                <?php endforeach; ?>
            </div>
        </form>
        </section>

        <!-- Affichage du message de félicitations si le jeu est terminé -->
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

    </main>

    <?php include 'includes/_footer.php'; ?>

</body>
</html>
