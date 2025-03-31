<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/mysqli.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_aluno'];
    $nome = $_POST['Nome'];
    $email = $_POST['Email'];
    $nr_processo = $_POST['nr_processo'];

    if (!empty($_FILES['novo_curriculo']['name'])) {
        $curriculo_nome = basename($_FILES['novo_curriculo']['name']);
        $curriculo_path = '../uploads/' . $curriculo_nome;
        move_uploaded_file($_FILES['novo_curriculo']['tmp_name'], $curriculo_path);

        $stmt = $conn->prepare("UPDATE alunos SET Nome = ?, Email = ?, nr_processo = ?, Curriculo = ? WHERE id_aluno = ?");
        $stmt->bind_param('ssssi', $nome, $email, $nr_processo, $curriculo_nome, $id);
    } else {
        $stmt = $conn->prepare("UPDATE alunos SET Nome = ?, Email = ?, nr_processo = ? WHERE id_aluno = ?");
        $stmt->bind_param('sssi', $nome, $email, $nr_processo, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Alterações guardadas com sucesso!";
        header("Location: admin_dashboard.php?page=gestao_utilizadores");
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
    <title>Editar Aluno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="form-box-editar">
    <h1>Editar Aluno</h1>
    <form method="POST" action="" enctype="multipart/form-data" id="editarAlunoForm">
        <input type="hidden" name="id_aluno" value="<?= htmlspecialchars($aluno['id_aluno']) ?>">

        <div class="input-group-editar">
            <label for="Nome">Nome do Aluno:</label>
            <input type="text" name="Nome" id="Nome" value="<?= htmlspecialchars($aluno['Nome']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="Email">Email do Aluno:</label>
            <input type="email" name="Email" id="Email" value="<?= htmlspecialchars($aluno['Email']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="nr_processo">Nº de Processo:</label>
            <input type="text" name="nr_processo" id="nr_processo" value="<?= htmlspecialchars($aluno['nr_processo']) ?>" required>
        </div>

        <div class="input-group-editar">
            <label>Currículo Atual:</label>
            <?php if (!empty($aluno['Curriculo'])): ?>
                <a href="../uploads/<?= htmlspecialchars($aluno['Curriculo']) ?>" download target="_blank" class="curriculo-link-editar" title="Transferir Currículo">
                    <i class="fas fa-file-download"></i> Ver Currículo Atual
                </a>
            <?php else: ?>
                <span class="no-cv-msg-editar">Nenhum currículo enviado</span>
            <?php endif; ?>
        </div>

        <div class="input-group-editar">
            <label for="novo_curriculo">Novo Currículo (PDF):</label>
            <input type="file" name="novo_curriculo" id="novo_curriculo" accept=".pdf">
        </div>

        <button type="submit" class="btn-editar-submit">Guardar Alterações</button>
    </form>
</div>

</body>
</html>
