<?php
session_start();

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: formlogin.php");
    exit();
}

// Define a página padrão como "dashboard"
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Admin</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
</head>
<body>
    <!-- Header -->
    <?php require "assets/elements/header.php"; ?>

    <!-- Dashboard Container -->
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="admin-profile">
                <img src="../images/professor.png" alt="Foto do Administrador" class="admin-profile-picture">
                <h3>Administrador</h3>
            </div>
            <div class="menu-items">
                <a href="admin_dashboard.php?pages=dashboard" class="menu-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Gerir Candidaturas
                </a>
                <a href="admin_dashboard.php?pages=gerir_ofertas" class="menu-item <?php echo $page === 'gerir_ofertas' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Publicar Ofertas
                </a>
                <a href="admin_dashboard.php?pages=gestao_utilizadores" class="menu-item <?php echo $page === 'gestao_utilizadores' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Gestão de Utilizadores
                </a>
                <a href="admin_dashboard.php?pages=gestao_ofertas" class="menu-item <?php echo $page === 'gestao_ofertas' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Gestão de Ofertas
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <?php
            // Inclui a página correta com base no parâmetro "page"
            $allowed_pages = ['dashboard', 'gerir_ofertas', 'gestao_utilizadores', 'gestao_ofertas'];
            if (in_array($page, $allowed_pages)) {
                include "pages/{$page}.php";
            } else {
                echo "<h1>Página não encontrada</h1>";
            }
            ?>
        </main>
    </div>
        <?php require "assets/elements/footer.php"; ?>
</body>
</html>
