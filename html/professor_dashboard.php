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
</head>
<body>
    <!-- Header -->
    <?php require "assets/elements/header.php"; ?>

    <!-- Sidebar -->
    <nav class="admin-sidebar professor-sidebar">
        <div class="admin-profile professor-profile">
            <!-- Foto do Professor -->
            <div class="profile-pic-container">
                <img src="<?php echo $foto; ?>" alt="Foto do Professor" class="admin-profile-picture">
                <!-- Ícone "+" para upload -->
                <label for="upload-foto" class="upload-icon">+</label>
            </div>

            <!-- Formulário de Upload -->
            <form action="upload_foto.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="upload-foto" name="foto" accept="image/*" required onchange="this.form.submit()">
            </form>
        </div>

        <div class="menu-items">
            <a href="professor_dashboard.php?page=dashboard" class="menu-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="professor_dashboard.php?page=gerir_ofertas" class="menu-item <?php echo $page === 'gerir_ofertas' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Publicar Oferta
            </a>
            <a href="professor_dashboard.php?page=gestao_ofertas" class="menu-item <?php echo $page === 'gestao_ofertas' ? 'active' : ''; ?>">
                <i class="fas fa-tasks"></i> Gerir Ofertas
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php
            $allowed_pages = ['dashboard', 'gerir_ofertas', 'gestao_ofertas'];
            if (in_array($page, $allowed_pages)) {
                include "pagesprofessores/{$page}.php";
            } else {
                echo "<h1>Página não encontrada</h1>";
            }
        ?>
    </main>

    <!-- Footer -->
    <?php require "assets/elements/footer.php"; ?>
</body>
</html>
