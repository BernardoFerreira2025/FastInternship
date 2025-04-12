<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
<!-- Logo -->
<a class="logo" href="index.php">
<img src="../images/logoesag.png" alt="Logo ESAG" class="logo-escola" />
    FastInternship
</a>

<!-- Botão hamburguer -->
<button class="menu-toggle" onclick="toggleMenu()">☰</button>

    <!-- Links de navegação com ícones -->
    <div class="nav-links" id="navLinks">
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <a href="about.php"><i class="fas fa-info-circle"></i> Sobre</a>
        <a href="contact.php"><i class="fas fa-envelope"></i> Contacto</a>
    </div>

    <!-- Ícones com dropdown -->
    <div class="auth-icons">
        <?php if (isset($_SESSION['username']) && isset($_SESSION['user_role'])):
            $userFirstName = explode(' ', trim($_SESSION['username']))[0];
            $role = $_SESSION['user_role'];

            // Caminho da dashboard com base no tipo de utilizador
            switch ($role) {
                case 'admin':
                    $dashboardLink = 'admin_dashboard.php';
                    break;
                case 'professor':
                    $dashboardLink = 'professor_dashboard.php';
                    break;
                case 'aluno':
                    $dashboardLink = 'aluno_dashboard.php';
                    break;
                case 'empresa':
                    $dashboardLink = 'empresa_dashboard.php';
                    break;
                default:
                    $dashboardLink = '#';
            }
        ?>
            <div class="dropdown-icone">
                <button class="dropdown-toggle-icone">
                    Olá, <?php echo htmlspecialchars($userFirstName); ?>
                </button>
                <div class="dropdown-menu-icone">
    <?php if ($role !== 'admin' && $role !== 'professor'): ?>
        <a href="perfil.php" class="dropdown-item-icone">
            <i class="fas fa-user"></i> Perfil
        </a>
    <?php endif; ?>

    <?php if ($role !== 'admin'): ?>
        <a href="seguranca.php" class="dropdown-item-icone">
            <i class="fas fa-shield-alt"></i> Segurança
        </a>
    <?php endif; ?>

    <?php if ($role !== 'admin'): ?>
        <a href="#" class="dropdown-item-icone">
            <i class="fas fa-bell"></i> Notificações
        </a>
        <a href="#" class="dropdown-item-icone">
            <i class="fas fa-comment-dots"></i> Chat
        </a>
    <?php endif; ?>

    <a href="<?= $dashboardLink ?>" class="dropdown-item-icone">
        <i class="fas fa-th-large"></i> Painel de Controlo
    </a>
    <a href="logout.php" class="dropdown-item-icone">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>
        <?php else: ?>
            <div class="auth-links">
                <a href="formlogin.php" class="btn-login">Login</a>
                <a href="signup.php" class="btn-signup">Registe-se</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<script>
function toggleMenu() {
    const nav = document.getElementById("navLinks");
    nav.classList.toggle("show");
}
</script>

