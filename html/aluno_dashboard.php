<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</body>
</html>
