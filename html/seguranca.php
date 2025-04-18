<?php
session_start();
include '../database/mysqli.php';

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['aluno', 'professor', 'empresa'])) {
    header("Location: formlogin.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$user_id = match($user_role) {
    'aluno' => $_SESSION['id_aluno'],
    'professor' => $_SESSION['id_professor'],
    'empresa' => $_SESSION['id_empresas'],
};

$tabela = match($user_role) {
    'aluno' => 'alunos',
    'professor' => 'professores',
    'empresa' => 'empresas',
};

$coluna_id = match($user_role) {
    'aluno' => 'id_aluno',
    'professor' => 'id_professor',
    'empresa' => 'id_empresas',
};

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = $_POST['nova_password'] ?? '';
    $confirmar = $_POST['confirmar_password'] ?? '';

    if ($nova !== $confirmar) {
        $erro = "As palavras-passe não coincidem.";
    } elseif (strlen($nova) < 4) {
        $erro = "A palavra-passe deve ter pelo menos 4 caracteres.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM $tabela WHERE $coluna_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($password_atual_hash);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($nova, $password_atual_hash)) {
            $erro = "A nova palavra-passe não pode ser igual à atual.";
        } else {
            $hash = password_hash($nova, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE $tabela SET password = ? WHERE $coluna_id = ?");
            $stmt->bind_param("si", $hash, $user_id);

            if ($stmt->execute()) {
                $sucesso = "Palavra-passe atualizada com sucesso!";
            } else {
                $erro = "Erro ao atualizar a palavra-passe.";
            }
        }
    }
}

// Define o destino para o botão "Voltar"
$voltar_para = match ($user_role) {
    'aluno' => 'aluno_dashboard.php',
    'professor' => 'professor_dashboard.php',
    'empresa' => 'empresa_dashboard.php',
};
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Alterar Palavra-passe</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'assets/elements/header.php'; ?>

<section class="seguranca-section">
    <div class="seguranca-container">
        <!-- Botão Voltar -->
        <div style="text-align: left; margin-bottom: 20px;">
            <a href="<?= $voltar_para ?>" class="back-button">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel de Controlo
            </a>
        </div>

        <h2>Alterar Palavra-passe</h2>

        <?php if ($erro): ?>
            <div class="popup error-popup"><?= $erro ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="popup success-popup"><?= $sucesso ?></div>
            <script>
                setTimeout(function() {
                    window.location.href = '<?= $voltar_para ?>';
                }, 3000);
            </script>
        <?php endif; ?>

        <form method="POST" class="password-form">
            <div class="input-group">
                <label for="nova_password">Nova Palavra-passe</label>
                <div class="password-box">
                    <input type="password" id="nova_password" name="nova_password" class="input-field" required>
                    <span class="toggle-view" onclick="togglePasswordVisibility('nova_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="input-group">
                <label for="confirmar_password">Confirmar Palavra-passe</label>
                <div class="password-box">
                    <input type="password" id="confirmar_password" name="confirmar_password" class="input-field" required>
                    <span class="toggle-view" onclick="togglePasswordVisibility('confirmar_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="signup-btn">Alterar</button>
        </form>
    </div>
</section>

<?php include 'assets/elements/footer.php'; ?>

<script>
function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
