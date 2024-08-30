<?php
session_start();


?>


<!DOCTYPE html>
<html lang="en">
<?php include 'includes/_head.php'; ?>

<body>

    <?php include 'includes/_header.php'; ?>

        <main>
            <h2>Bienvenu sur Memory!</h2>
            <form method="post" action="index.php">
                <label>Nouvelle partie :</label><br>
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
                </select><br>
                <input type="submit" value="Lancer la partie">
            </form>
        </main>

    <?php include 'includes/_footer.php'; ?>

</body>
</html>