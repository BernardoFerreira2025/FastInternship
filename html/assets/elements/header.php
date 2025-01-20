<?php
// Verificar se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <!-- Logo -->
    <a class="logo" href="index.php">FastInternship</a>
    
    <!-- Links de navegação -->
    <div class="nav-links">
        <a href="index.php">Início</a>
        <a href="about.php">Sobre</a>
        <a href="contact.php">Contacto</a>
    </div>
    
    <!-- Links dinâmicos -->
    <div class="auth-links">
        <?php
        if (isset($_SESSION['username'])): 
            $userFirstName = explode(' ', trim($_SESSION['username']))[0]; // Primeiro nome
        ?>
            <div class="dropdown">
                <button class="dropdown-toggle btn-user" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Bem-vindo, <?php echo htmlspecialchars($userFirstName); ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                    <li><a class="dropdown-item" href="definicoes.php">Definições</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="auth-links">
                <a href="formlogin.php" class="btn-login">Login</a>
                <a href="signup.php" class="btn-signup">Registe-se</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
