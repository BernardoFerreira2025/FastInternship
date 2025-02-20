<?php
include '../../database/mysqli.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_aluno'];
    $nome = $_POST['Nome'];
    $email = $_POST['Email'];
    $curso = $_POST['Curso'];

    $stmt = $conn->prepare("UPDATE alunos SET Nome = ?, Email = ?, Curso = ? WHERE id_aluno = ?");
    $stmt->bind_param('sssi', $nome, $email, $curso, $id);

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
</head>
<body>
    <div class="form-container">
        <h1>Editar Aluno</h1>
        <form method="POST" action="editar_aluno.php">
            <input type="hidden" name="id_aluno" value="<?= htmlspecialchars($aluno['id_aluno']) ?>">
            <label>Nome do Aluno:</label>
            <input type="text" name="Nome" value="<?= htmlspecialchars($aluno['Nome']) ?>" required>
            <label>Email do Aluno:</label>
            <input type="email" name="Email" value="<?= htmlspecialchars($aluno['Email']) ?>" required>
            <label>Curso:</label>
            <input type="text" name="Curso" value="<?= htmlspecialchars($aluno['Curso']) ?>" required>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
