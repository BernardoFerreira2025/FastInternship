<?php
session_start();
include '../database/mysqli.php';

// Verifica se o usuário está logado e é um professor
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'professor') {
    header("Location: formlogin.php");
    exit();
}

// Buscar informações do professor no banco de dados
$id_professor = $_SESSION['id_professor'];
$query = "SELECT nome, foto FROM professores WHERE id_professor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_professor);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();

// Define o caminho da foto
$foto = !empty($professor['foto']) ? '../images/' . $professor['foto'] : '../images/professor.png';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php require "assets/elements/header.php"; ?>

    <
       <!-- Sidebar -->
        <nav class="sidebar">
            <div class="profile">
            <div class="profile-pic-container">
                    <img src="<?php echo $foto_admin; ?>">
                    <label for="upload-foto" class="upload-icon"><i class="fas fa-camera"></i></label>
                </div>
                <h3>Olá, <?php echo $professor['nome']; ?></h3>

                <!-- Formulário de Upload -->
                <form action="upload_foto.php" method="POST" enctype="multipart/form-data">
                    <input type="file" id="upload-foto" name="foto" accept="image/*" required onchange="this.form.submit()">
                </form>
            </div>

            <ul class="menu">
                <li><a href="professor_dashboard.php?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="professor_dashboard.php?page=adicionar_empresa" class="<?php echo $page === 'adicionar_empresa' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i> Adicionar Empresa</a></li>
                <li><a href="professor_dashboard.php?page=gerir_ofertas" class="<?php echo $page === 'gerir_ofertas' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i> Publicar Oferta</a></li>
                <li><a href="professor_dashboard.php?page=gestao_ofertas" class="<?php echo $page === 'gestao_ofertas' ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i> Gerir Ofertas</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <?php
                $allowed_pages = ['dashboard', 'gerir_ofertas', 'gestao_ofertas', 'adicionar_empresa'];
                if (in_array($page, $allowed_pages)) {
                    include "pagesprofessores/{$page}.php";
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
