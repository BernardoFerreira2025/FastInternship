<?php
include '../../database/mysqli.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_aluno'];
    $nome = $_POST['Nome'];
    $email = $_POST['Email'];
    $nr_processo = $_POST['nr_processo'];

    // Verifica se foi enviado um novo currículo
    if (!empty($_FILES['novo_curriculo']['name'])) {
        $curriculo_nome = basename($_FILES['novo_curriculo']['name']);
        $curriculo_path = '../uploads/' . $curriculo_nome;
        move_uploaded_file($_FILES['novo_curriculo']['tmp_name'], $curriculo_path);

        // Atualiza com currículo
        $stmt = $conn->prepare("UPDATE alunos SET Nome = ?, Email = ?, nr_processo = ?, Curriculo = ? WHERE id_aluno = ?");
        $stmt->bind_param('ssssi', $nome, $email, $nr_processo, $curriculo_nome, $id);
    } else {
        // Atualiza sem currículo
        $stmt = $conn->prepare("UPDATE alunos SET Nome = ?, Email = ?, nr_processo = ? WHERE id_aluno = ?");
        $stmt->bind_param('sssi', $nome, $email, $nr_processo, $id);
    }

    if ($stmt->execute()) {
        session_start();
        $_SESSION['toast_message'] = "Alterações guardadas com sucesso!";
        header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
        exit();        
    } else {
        echo "Erro ao atualizar os dados.";
    }    
} else {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM alunos WHERE id_aluno = $id");
    $aluno = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <h1 class="gradient-text">Editar Aluno</h1>
        <form method="POST" action="editar_aluno.php" enctype="multipart/form-data">
            <input type="hidden" name="id_aluno" value="<?= htmlspecialchars($aluno['id_aluno']) ?>">

            <label>Nome do Aluno:</label>
            <input type="text" name="Nome" value="<?= htmlspecialchars($aluno['Nome']) ?>" required>

            <label>Email do Aluno:</label>
            <input type="email" name="Email" value="<?= htmlspecialchars($aluno['Email']) ?>" required>

            <label>Nº de Processo:</label>
            <input type="text" name="nr_processo" value="<?= htmlspecialchars($aluno['nr_processo']) ?>" required>

            <label>
    Currículo Atual:
    <?php if (!empty($aluno['Curriculo'])): ?>
        <a href="../uploads/<?= htmlspecialchars($aluno['Curriculo']) ?>" download target="_blank" title="Transferir Currículo">
            <i class="fas fa-file-download" style="color: #4f8cff; font-size: 1.3rem; margin-left: 8px;"></i>
        </a>
    <?php else: ?>
        <span class="no-cv">Nenhum currículo enviado</span>
    <?php endif; ?>
</label>

            <label>Novo Currículo (PDF):</label>
            <input type="file" name="novo_curriculo" accept=".pdf">

            <button type="submit" class="btn-upload">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
