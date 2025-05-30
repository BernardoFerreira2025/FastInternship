<?php
session_start();
include '../database/mysqli.php';

// Verifica se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: formlogin.php");
    exit();
}

// Buscar informações do administrador na tabela correta (utilizadores)
$username = $_SESSION['username']; 
$query = "SELECT id_utilizador, username, foto FROM utilizadores WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Garante que o id do admin fica guardado na sessão (necessário para o upload funcionar)
$_SESSION['id_admin'] = $admin['id_utilizador'];

// Define o nome e a foto do administrador
$nome_admin = $admin['username'] ?? 'Administrador';
$foto_admin = !empty($admin['foto']) ? '../images/' . $admin['foto'] : '../images/admin_default.png';

// Define a página padrão
$page = isset($_GET['page']) ? $_GET['page'] : 'gestao_utilizadores';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controlo - Admin</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php require "assets/elements/header.php"; ?>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="profile">
            <div class="profile-pic-container">
                <img src="<?php echo htmlspecialchars($foto_admin); ?>"  class="foto-perfil">
                <label for="upload-foto" class="upload-icon"><i class="fas fa-camera"></i></label>
            </div>

            <!-- Formulário de Upload -->
            <form action="upload_foto.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="upload-foto" name="foto" accept="image/*" required onchange="this.form.submit()">
            </form>
        </div>

        <!-- Menu de Navegação -->
        <ul class="menu">
            <li><a href="admin_dashboard.php?page=adicionar_professor" class="<?php echo $page === 'adicionar_professor' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Adicionar Professor</a></li>
            <li><a href="admin_dashboard.php?page=gestao_utilizadores" class="<?php echo $page === 'gestao_utilizadores' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Gestão de Utilizadores</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        $allowed_pages = ['gestao_utilizadores', 'adicionar_professor', 'editar_aluno', 'editar_empresa', 'editar_professor'];

        if (in_array($page, $allowed_pages)) {
            $file_path = "pages/{$page}.php";
            if (file_exists($file_path)) {
                include $file_path;
            } else {
                echo "<h1>Erro: Página não encontrada.</h1>";
            }
        } else {
            echo "<h1>Página não encontrada</h1>";
        }
        ?>
    </main>

    <!-- Footer -->
    <?php require "assets/elements/footer.php"; ?>
</body>
</html>
