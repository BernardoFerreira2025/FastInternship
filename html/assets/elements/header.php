<?php
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
                    <i class="fas fa-user-circle"></i> Olá, <?php echo htmlspecialchars($userFirstName); ?>
                    <i class="fas fa-caret-down"></i>
                </button>
                <div class="dropdown-menu-icone">
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
            </div>
        <?php else: ?>
            <div class="auth-links">
                <a href="formlogin.php" class="btn-login">Login</a>
                <a href="signup.php" class="btn-signup">Registe-se</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
