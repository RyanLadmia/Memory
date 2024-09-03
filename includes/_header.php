<?php
if (File_Name == "index"){
    $path = "./";
    $path2 ="./pages/";
}
else{
    $path = "../";
    $path2 = "./";
}
?>

<header>
    <nav class="Navbar">
        <ul>
            <li><a href=" <?php echo $path; ?>index.php">Accueil</a></li>
            <li><a href=" <?php echo $path2; ?>classement.php">Classement</a></li>
            <li><a href=" <?php echo $path2; ?>profil.php">Profil</a></li>
        </ul>
    </nav>
</header>