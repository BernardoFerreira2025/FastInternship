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

// Define o nome e a foto do aluno
$nome_aluno = $aluno['nome'] ?? 'Aluno';
$foto_aluno = !empty($aluno['foto']) ? '../images/' . $aluno['foto'] : '../images/aluno.png';

// Capturar apenas o primeiro e o último nome
$nome_partes = explode(" ", trim($nome_aluno));
$primeiro_nome = $nome_partes[0]; // Primeiro nome
$ultimo_nome = end($nome_partes); // Último nome

// Define a página padrão
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php require "assets/elements/header.php"; ?>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="profile">
            <div class="profile-pic-container">
                <img src="<?php echo $foto; ?>">
                <label for="upload-foto" class="upload-icon"><i class="fas fa-camera"></i></label>
            </div>
            <h3>Olá, <?php echo htmlspecialchars($primeiro_nome . " " . $ultimo_nome); ?></h3>

            <!-- Formulário de Upload -->
            <form action="upload_foto_aluno.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="upload-foto" name="foto" accept="image/*" required onchange="this.form.submit()">
            </form>
        </div>

        <!-- Menu de Navegação -->
        <ul class="menu">
            <li><a href="aluno_dashboard.php?page=verofertas" class="<?php echo $page === 'verofertas' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i> Ver Ofertas</a></li>
            <li><a href="aluno_dashboard.php?page=historico" class="<?php echo $page === 'historico' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Histórico de Candidaturas</a></li>
                <li><a href="aluno_dashboard.php?page=candidatura_final" class="<?php echo $page === 'candidatura_final' ? 'active' : ''; ?>">
                <i class="fas fa-check-double"></i> Candidaturas Analisadas</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php
            // Lista de páginas permitidas
            $allowed_pages = ['verofertas', 'historico', 'candidatar', 'candidatura_final'];
            if (in_array($page, $allowed_pages)) {
                $file_path = "pagesalunos/{$page}.php";
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
