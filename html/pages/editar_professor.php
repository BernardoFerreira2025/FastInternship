<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/mysqli.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_professor'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $id_curso = $_POST['id_curso'];

    // Atualiza os dados do professor
    $stmt = $conn->prepare("UPDATE professores SET nome = ?, email = ?, id_curso = ? WHERE id_professor = ?");
    $stmt->bind_param("ssii", $nome, $email, $id_curso, $id);

    if ($stmt->execute()) {
        // Define mensagem de sucesso na sessão
        $_SESSION['mensagem_sucesso'] = "Alterações guardadas com sucesso!";
        // Redireciona para gestao_utilizadores.php
        $_SESSION['toast_message'] = "Alterações guardadas com sucesso!";
        header("Location: admin_dashboard.php?page=gestao_utilizadores");
        exit();        
    } else {
        echo "Erro ao atualizar os dados.";
    }
} else {
    // Verifica se o ID foi passado
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Buscar os dados do professor
        $result = $conn->query("SELECT * FROM professores WHERE id_professor = $id");
        if ($result->num_rows > 0) {
            $professor = $result->fetch_assoc();
        } else {
            echo "Professor não encontrado.";
            exit();
        }
    } else {
        echo "ID do professor não especificado.";
        exit();
    }

    // Obtém os cursos para o dropdown
    $cursos = $conn->query("SELECT id_curso, nome FROM cursos");
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Professor</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="form-box-editar">
    <h1>Editar Professor</h1>
    <form method="POST" action="" id="editarProfessorForm">
        <input type="hidden" name="id_professor" value="<?= htmlspecialchars($professor['id_professor']); ?>">

        <div class="input-group-editar">
            <label for="nome"><i class="fas fa-user"></i> Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($professor['nome']); ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($professor['email']); ?>" required>
        </div>

        <div class="input-group-editar">
            <label for="id_curso"><i class="fas fa-graduation-cap"></i> Curso:</label>
            <select id="id_curso" name="id_curso" required>
                <?php while ($curso = $cursos->fetch_assoc()) { ?>
                    <option value="<?= $curso['id_curso']; ?>" <?= ($professor['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn-editar-submit">Guardar Alterações</button>
    </form>
</div>

</body>
</html>

