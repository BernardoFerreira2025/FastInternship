<?php
session_start();
include '../../database/mysqli.php'; // Conexão com o banco de dados

// Verifica se o usuário veio do login e armazenou um e-mail válido
if (!isset($_SESSION['email_temp'])) {
    $_SESSION['error'] = "Acesso negado! Insira o seu e-mail primeiro.";
    header("Location: ../formlogin.php");
    exit();
}

$email = $_SESSION['email_temp'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email_input = trim($_POST['email']);
    $nova_senha = trim($_POST['nova_senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);

    // Verifica se o e-mail digitado corresponde ao do login
    if ($email !== $email_input) {
        $_SESSION['error'] = "O e-mail inserido não corresponde ao do login.";
        header("Location: reset_password.php");
        exit();
    }

    // Verifica se as senhas coincidem
    if ($nova_senha !== $confirmar_senha) {
        $_SESSION['error'] = "As senhas não coincidem.";
        header("Location: reset_password.php");
        exit();
    }

    // Criptografa a nova senha
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualiza a senha na tabela correta
    $stmt = $conn->prepare("UPDATE alunos SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $senha_hash, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Palavra-passe alterada com sucesso! Faça login.";
        header("Location: ../formlogin.php");
    } else {
        $_SESSION['error'] = "Erro ao atualizar a senha. Tente novamente.";
        header("Location: reset_password.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Palavra-Passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="../assets/elements/header.css">
    <link rel="stylesheet" href="../assets/elements/footer.css">
</head>
<body>
        <?php require "../assets/elements/header.php"; ?>
    <main class="auth-container d-flex justify-content-center align-items-center">
        <div class="auth-card">
            <h2 class="text-center">Redefinir Palavra-Passe</h2>
            <p class="text-center">Insira sua nova senha</p>

            <!-- Mensagem de erro -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="toast-message toast-error" id="toast-message">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="reset_password.php" method="POST">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
                </div>

                <button type="submit" class="login-btn">Alterar Palavra-Passe</button>
            </form>
        </div>
    </main>

    <script>
    // Remover toast após 4 segundos
    setTimeout(function() {
        let toast = document.getElementById("toast-message");
        if (toast) {
            toast.style.opacity = "0";
            setTimeout(() => toast.remove(), 500);
        }
    }, 4000);
    </script>
        <?php require "../assets/elements/footer.php"; ?>
</body>
</html>
