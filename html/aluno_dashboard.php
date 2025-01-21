<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
</head>
<body>
    <!-- Header -->
    <?php require "assets/elements/header.php"; ?>

    <!-- Dashboard Container -->
    <div class="admin-dashboard aluno-dashboard">
        <!-- Sidebar -->
        <nav class="admin-sidebar aluno-sidebar">
            <div class="admin-profile aluno-profile">
                <img src="../images/aluno.png" alt="Foto do Aluno" class="admin-profile-picture">
            </div>
            <div class="menu-items">
                <a href="aluno_dashboard.php?page=verofertas" class="menu-item <?php echo $page === 'verofertas' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Ver Ofertas
                </a>
                <a href="aluno_dashboard.php?page=historico" class="menu-item <?php echo $page === 'historico' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> Histórico de Candidaturas
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <?php
            // Inclui a página correta com base no parâmetro "page"
            $allowed_pages = ['verofertas', 'historico'];
            if (in_array($page, $allowed_pages)) {
                include "../pagesalunos/{$page}.php";
            } else {
                echo "<h1>Página não encontrada</h1>";
            }
            ?>
        </main>
    </div>

    <!-- Footer -->
    <?php require "assets/elements/footer.php"; ?>
</body>
</html>
