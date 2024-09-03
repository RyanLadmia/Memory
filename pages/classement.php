<?php

// Inclure la connexion à la base de données
include '../includes/_connect.php';

// Créer une instance de la classe Database
$db = new Database();

// Connexion à la base de données
$pdo = $db->connect();

// Requête pour récupérer les meilleurs scores
$stmt = $pdo->prepare('SELECT login, score FROM scores ORDER BY score DESC LIMIT 10');
$stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

    <?php include '../includes/_head.php'; ?>

<body class="classement">

    <?php include '../includes/_header.php'; ?>

    <br><br><br>

    <h3>Top Scores :</h3>
    <table class="cla1">
        <tr>
            <th>Pseudonyme</th>
            <th>Score</th>
        </tr>
        <?php foreach ($scores as $score): ?>
            <tr>
                <td><?php echo htmlspecialchars($score['login']); ?></td>
                <td><?php echo htmlspecialchars(number_format($score['score'], 2)); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
