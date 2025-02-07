<?php
session_start();
include '../database/mysqli.php';

// Verificar se o usuário está logado e é um aluno
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'aluno') {
    header("Location: formlogin.php");
    exit();
}

// Buscar informações do aluno no banco de dados
$id_aluno = $_SESSION['id_aluno'];
$query = "SELECT nome, foto FROM alunos WHERE id_aluno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

// Define o caminho da foto
$foto = !empty($aluno['foto']) ? '../images/' . $aluno['foto'] : '../images/aluno.png';

$page = isset($_GET['page']) ? $_GET['page'] : 'verofertas';
?>
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
                <!-- Foto do Aluno -->
                  <div class="profile-pic-container">
                    <img src="<?php echo $foto; ?>" alt="Foto do Aluno" class="admin-profile-picture">
                     <!-- Ícone "+" para upload -->
                    <label for="upload-foto" class="upload-icon">+</label>
                  </div>

                   <!-- Formulário de Upload -->
                 <form action="upload_foto_aluno.php" method="POST" enctype="multipart/form-data">
                      <input type="file" id="upload-foto" name="foto" accept="image/*" required onchange="this.form.submit()">
                 </form>
            </div>
            <div class="menu-items">
                 <?php
                    // Definir a variável $page com um valor padrão se ela não estiver definida
                    $page = isset($_GET['page']) ? $_GET['page'] : 'verofertas';
                ?>
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
                $allowed_pages = ['verofertas', 'historico', 'candidatar'];
                if (in_array($page, $allowed_pages)) {
                    include "pagesalunos/{$page}.php";
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