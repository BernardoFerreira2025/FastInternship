<?php
session_start();
include '../database/mysqli.php';

// Verifica se a empresa está logada corretamente
if (!isset($_SESSION['id_empresas']) || $_SESSION['user_role'] !== 'empresa') {
    header("Location: formlogin.php");
    exit();
}

// Buscar informações da empresa no banco de dados
$id_empresas = $_SESSION['id_empresas'];
$query = "SELECT nome_empresa, foto FROM empresas WHERE id_empresas = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_empresas);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

// Define o caminho da foto
$foto = !empty($empresa['foto']) ? '../images/' . $empresa['foto'] : '../images/empresa.png';

// Pega a página solicitada via GET
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Empresa</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <?php require "assets/elements/header.php"; ?>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="profile">
                <div class="profile-pic-container">
                    <img src="<?php echo $foto; ?>">
                    <label for="upload-foto" class="upload-icon"><i class="fas fa-camera"></i></label>
                </div>
              
                <!-- Formulário de Upload -->
                <form action="upload_foto.php" method="POST" enctype="multipart/form-data">
                    <input type="file" id="upload-foto" name="foto" accept="image/*" required onchange="this.form.submit()">
                </form>
            </div>

            <ul class="menu">
                <li><a href="empresa_dashboard.php?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="empresa_dashboard.php?page=gestao_ofertas" class="<?php echo $page === 'gestao_ofertas' ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Gestão das Ofertas</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <?php
                $allowed_pages = ['dashboard', 'gestao_ofertas', 'alunos_candidatos', 'editar_oferta'];
                if (in_array($page, $allowed_pages)) {
                    include "pagesempresas/{$page}.php";
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
