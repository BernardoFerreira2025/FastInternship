<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../database/mysqli.php';
//Verifica se o admin está com o login efetuado
if (!isset($_SESSION['id_utilizador'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}
// Obtém os cursos disponíveis
$cursos = $conn->query("SELECT id_curso, nome FROM cursos");

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id_curso = intval($_POST['id_curso']);

    // Validação do domínio do email
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@esag-edu\.net$/", $email)) {
        $_SESSION['toast_message'] = "Erro: Apenas emails @esag-edu.net são permitidos!";
        $_SESSION['toast_type'] = "toast-error";
        header("Location: admin_dashboard.php?page=adicionar_professor");
        exit();
    }

    // Verificar se o email já existe
    $verifica = $conn->prepare("SELECT id_professor FROM professores WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        $_SESSION['toast_message'] = "Erro: Este professor já está registado!";
        $_SESSION['toast_type'] = "toast-error";
        header("Location: admin_dashboard.php?page=adicionar_professor");
        exit();
    }

    // Insere os dados na tabela `professores`
    $sql = "INSERT INTO professores (nome, email, password, id_curso) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $email, $senha, $id_curso);

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Professor adicionado com sucesso!";
        $_SESSION['toast_type'] = "toast-success";
        header("Location: admin_dashboard.php?page=gestao_utilizadores");
        exit();
    } else {
        $_SESSION['toast_message'] = "Erro ao adicionar professor.";
        $_SESSION['toast_type'] = "toast-error";
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
        <div id="toast" class="toast-message <?php echo $_SESSION['toast_type']; ?>">
            <?php echo $_SESSION['toast_message']; ?>
        </div>
        <?php unset($_SESSION['toast_message'], $_SESSION['toast_type']); ?>
    <?php endif; ?>
    <div class="users-container">
        <h2>Adicionar Professor</h2>
        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Senha:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i> <!-- Ícone do olho -->
            </div>

            <label>Curso:</label>
            <select name="id_curso" required>
                <?php while ($curso = $cursos->fetch_assoc()) { ?>
                    <option value="<?php echo $curso['id_curso']; ?>">
                        <?php echo htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit" class="btn-upload">Adicionar</button>
        </form>
    </div>

    <script>
         // Validação no frontend
         document.getElementById("ProfForm").addEventListener("submit", function(event) {
            let emailInput = document.getElementById("email").value;

            if (!emailInput.endsWith("@esag-edu.net")) {
                showToast("Apenas e-mails @esag-edu.net são permitidos.", "error");
                event.preventDefault();
                return;
            }
        // Exibir e ocultar toast automaticamente
        document.addEventListener("DOMContentLoaded", function() {
            let toast = document.getElementById("toast");
            if (toast) {
                setTimeout(function() {
                    toast.style.display = "none";
                }, 4000);
            }
        });

                // Alternar visibilidade da senha
                document.getElementById("togglePassword").addEventListener("click", function() {
            let passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                this.classList.replace("fa-eye-slash", "fa-eye");
            }
        });
    </script>
    

</body>
</html>
