<?php
// Supposons que vous passez le nom de la page dans une variable $File_Name
$File_Name = basename($_SERVER['PHP_SELF'], ".php");

if ($File_Name == "index") {
    $path = "./";
    $path2 = "./pages/";
} else {
    $path = "../";
    $path2 = "./";
}
?>

<header>
    <nav class="Navbar">
        <ul>
            <li><a href="<?php echo $path; ?>index.php">Accueil</a></li>
            <li><a href="<?php echo $path2; ?>classement.php">Classement</a></li>
            <li><a href="<?php echo $path2; ?>profil.php">Profil</a></li>
        </ul>
    </nav>
</header>
