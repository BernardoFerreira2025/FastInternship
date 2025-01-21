<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
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
=======
    <title>Painel de Controlo - Aluno</title>
    <link rel="stylesheet" href="assets/css/aluno_dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/elements/header.css"> <!-- Certifique-se do caminho correto -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php require "assets/elements/header.php"; ?>
<section>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="student-sidebar">
            <div class="user-profile">
                <div class="profile-image">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="user-info">
                    <p>Portal do Estudante</p>
                </div>
            </div>
            <div class="menu-items">
                <a href="aluno_dashboard.php" class="menu-item active">
                    <i class="fas fa-home"></i> Painel de Controlo
                </a>
                <a href="verofertas_alunos.php" class="menu-item">
                    <i class="fas fa-file-alt"></i> Ver Ofertas
                </a>
                <a href="meu_perfil.php" class="menu-item">
                    <i class="fas fa-user"></i> Meu Perfil
                </a>
                <a href="configuracoes.php" class="menu-item">
                    <i class="fas fa-cog"></i> Configurações
                 </a>
            </div>
        </nav>
<section>
        <!-- Main Content -->
        <main class="main-content">
            <h1>Bem-vindo ao Portal do Estudante</h1>
            <p>Acompanhe suas candidaturas e explore novas oportunidades.</p>
        </main>
    </div>
    <script src="../js/auth.js"></script>
>>>>>>> 9cdd88bbd5c6b13318b6824c8b6210c86cc24075
</body>
</html>
