<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../database/mysqli.php';

// Obtém os cursos disponíveis
$cursos = $conn->query("SELECT id_curso, nome FROM cursos");

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encripta a senha
    $id_curso = intval($_POST['id_curso']);

    // Insere os dados na tabela `professores`
    $sql = "INSERT INTO professores (nome, email, password, id_curso) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $email, $senha, $id_curso);

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Professor adicionado com sucesso!";
        header("Location: admin_dashboard.php?page=gestao_utilizadores"); // Redireciona
        exit();
    } else {
        $_SESSION['toast_message'] = "Erro ao adicionar professor.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <title>Adicionar Professor</title>
</head>
<body>

    <!-- Exibe a mensagem de toast -->
    <?php if (isset($_SESSION['toast_message'])): ?>
        <div id="toast" class="toast-message toast-success"><?php echo $_SESSION['toast_message']; ?></div>
        <?php unset($_SESSION['toast_message']); ?>
    <?php endif; ?>

    <div class="form-container">
        <h2>Adicionar Professor</h2>
        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Senha:</label>
            <input type="password" name="password" required>

            <label>Curso:</label>
            <select name="id_curso" required>
                <?php while ($curso = $cursos->fetch_assoc()) { ?>
                    <option value="<?php echo $curso['id_curso']; ?>">
                        <?php echo htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Adicionar</button>
        </form>
    </div>

    <script>
        // Exibir e ocultar toast automaticamente
        document.addEventListener("DOMContentLoaded", function() {
            let toast = document.getElementById("toast");
            if (toast) {
                setTimeout(function() {
                    toast.style.display = "none";
                }, 4000);
            }
        });
    </script>

</body>
</html>
