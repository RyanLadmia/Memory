<?php
session_start();

require_once '../php/classes.php';

$msg = '';
$isLoggedIn = isset($_SESSION['login']) && !empty($_SESSION['login']);
$showForm = '';
$showConfirmationButtons = false;

// Initialize the form to show
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    
    if (isset($_POST['register_button'])) {
        if ($login && $password) {
            $msg = $user->register($login, $password);
            if ($msg === "Inscription réussie. Vous pouvez maintenant vous connecter.") {
                $showForm = ''; // Hide all forms after successful registration
            }
        } else {
            $msg = "Veuillez remplir tous les champs.";
        }
    } elseif (isset($_POST['login_submit'])) {
        if ($login && $password) {
            $result = $user->login($login, $password);
            if ($result === "Connexion réussie.") {
                $isLoggedIn = true;
                $_SESSION['login'] = $login;
                $msg = $result;
                $showForm = ''; // Hide all forms after successful login
            } else {
                $msg = $result;
            }
        } else {
            $msg = "Veuillez remplir tous les champs.";
        }
    } elseif (isset($_POST['update_button'])) {
        if ($login && $password) {
            $msg = $user->update($login, $password);
        } else {
            $msg = "Veuillez remplir tous les champs.";
        }
    } elseif (isset($_POST['logout_button'])) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $isLoggedIn = false;
        $msg = "Vous avez été déconnecté.";
        $showForm = ''; // Hide all forms after logout
    } elseif (isset($_POST['delete_button'])) {
        $showConfirmationButtons = true;
    } elseif (isset($_POST['confirm_delete'])) {
        $msg = $user->delete();
        $isLoggedIn = false;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $msg = "Votre compte a bien été supprimé.";
        $showConfirmationButtons = false;
        $showForm = ''; // Hide all forms after account deletion
    } elseif (isset($_POST['cancel_delete'])) {
        $msg = "Suppression annulée.";
        $showConfirmationButtons = false;
    } elseif (isset($_POST['show_login'])) {
        $showForm = 'login';
    } elseif (isset($_POST['show_register'])) {
        $showForm = 'register';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include '../includes/_head.php'; ?>

<body class="profil">

<?php include '../includes/_header.php'; ?>

<main>
    <?php if (!$isLoggedIn): ?>
        <div>
            <!-- Buttons for login and register -->
            <form class="prof1" method="post">
                <button id="co" type="submit" name="show_login" <?php echo $showForm === 'login' ? 'disabled' : ''; ?>>Connexion</button>
                <button id="regis" type="submit" name="show_register" <?php echo $showForm === 'register' ? 'disabled' : ''; ?>>Inscription</button>
            </form>

            <!-- Login form -->
            <?php if ($showForm === 'login'): ?>
                <div id="login-form" class="form-container active">
                    <h2>Connexion</h2>
                    <form  class="prof2" method="post">
                        <label for="login">Pseudonyme</label><br>
                        <input type="text" name="login" placeholder="Login" required><br><br>
                        <label for="password">Mot de passe</label><br>
                        <input type="password" name="password" placeholder="Mot de passe" required><br><br>
                        <button id="p2co" type="submit" name="login_submit">Se connecter</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Register form -->
            <?php if ($showForm === 'register'): ?>
                <div id="register-form" class="form-container active">
                    <h2>Inscription</h2>
                    <form class="prof3" method="post">
                        <label for="login">Pseudonyme</label><br>
                        <input type="text" name="login" placeholder="Login" required><br><br>
                        <label for="password">Mot de passe</label><br>
                        <input type="password" name="password" placeholder="Mot de passe" required><br><br>
                        <button id="p3regis" type="submit" name="register_button">S'inscrire</button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    <?php else: ?>
        <div>
            <h2>Bienvenue, <?= htmlspecialchars($_SESSION['login']); ?></h2>
            <form class="prof4" method="post">
                <label for="login">Nouveau Pseudonyme</label><br>
                <input type="text" name="login" placeholder="Login" required><br><br>
                <label for="password">Nouveau Mot de passe</label><br>
                <input type="password" name="password" placeholder="Mot de passe" required><br><br>
                <button id="p4up" type="submit" name="update_button">Mettre à jour</button>
            </form><br>

            <form class="prof5" method="post">
                <button id="p5deco" type="submit" name="logout_button">Se déconnecter</button>
            </form><br>

            <!-- Bouton de suppression de compte -->
            <?php if (!$showConfirmationButtons): ?>
                <form class="prof6" method="post">
                    <button id="p6suppr" type="submit" name="delete_button">Supprimer mon compte</button>
                </form>
            <?php else: ?>
                <form class="prof7" method="post">
                    <p>Êtes-vous sûr de vouloir supprimer votre compte ?</p>
                    <button id="p7yes" type="submit" name="confirm_delete">Oui</button>
                    <button id="p7no" type="submit" name="cancel_delete">Non</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($msg) echo "<p>$msg</p>"; ?>
</main>

</body>
</html>
