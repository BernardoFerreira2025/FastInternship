<?php
session_start(); // Inicia a sessão para mensagens
include '../../database/mysqli.php'; // Conexão com o banco de dados

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
        header("Location: ../admin_dashboard.php?page=gestao_utilizadores");
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
    <link rel="stylesheet" href="../assets/css/allcss.css"> <!-- Link para o CSS -->
</head>
<body>
    <div class="form-container">
        <h1>Editar Professor</h1>
        <form action="editar_professor.php" method="POST">
            <input type="hidden" name="id_professor" value="<?php echo htmlspecialchars($professor['id_professor']); ?>">
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($professor['nome']); ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($professor['email']); ?>" required>
            </div>
            <div>
                <label for="id_curso">Curso:</label>
                <select id="id_curso" name="id_curso" required>
                    <?php while ($curso = $cursos->fetch_assoc()) { ?>
                        <option value="<?php echo $curso['id_curso']; ?>" <?php if ($professor['id_curso'] == $curso['id_curso']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($curso['nome']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit">Guardar Alterações</button>
        </form>
        <!-- Removido o botão "Voltar" -->
    </div>
</body>
</html>
