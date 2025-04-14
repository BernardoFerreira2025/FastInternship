<?php
include '../database/mysqli.php'; // Conexão com o banco de dados

// Buscar os cursos disponíveis no banco de dados
$sql_cursos = "SELECT id_curso, nome FROM cursos";
$result_cursos = $conn->query($sql_cursos);

$cursos = [];
if ($result_cursos->num_rows > 0) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - FastInternship</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <?php require "assets/elements/header.php"; ?>
    
        <!-- Exibir toast caso a senha esteja incorreta -->
        <?php if (isset($_SESSION['error'])): ?>
        <div class="toast-message toast-error" id="toast-message">
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); // Remove o erro após exibir ?>
        <?php endif; ?>

   <!-- Substitui esta parte pela seguinte já formatada -->
<section class="signup-wrapper">
    <div class="signup-box">
        <h2>Crie a Sua Conta</h2>
        <p>Crie a sua conta para aceder às melhores oportunidades de estágio</p>
        <form id="registerForm" action="signuphandler.php" method="POST" enctype="multipart/form-data">

            <div class="signup-group">
                <label for="name">Nome Completo</label>
                <input type="text" id="name" name="name" value="Luís Pinto" placeholder="Escreva o seu nome completo" required>
            </div>

            <div class="signup-group">
                <label for="processNumber">Número de Processo</label>
                <input type="text" id="processNumber" name="processNumber" value="28123" placeholder="Escreva o seu número de processo" required>
            </div>

            <div class="signup-group">
                <label for="class">Turma</label>
                <input type="text" id="class" name="class" value="12K" placeholder="Escreva a sua turma" required>
            </div>

            <div class="signup-group">
                <label for="id_curso">Curso</label>
                <select id="id_curso" name="id_curso" required>
                    <option value="" disabled selected>Selecione o seu curso</option>
                    <?php foreach ($cursos as $curso): ?>
                        <?php $select=""; if($curso['id_curso']==1){ $select="selected"; } ?>
                        <option <?php echo $select; ?> value="<?php echo $curso['id_curso']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="signup-group">
                <label for="birthDate">Data de Nascimento</label>
                <input type="date" id="birthDate" value="2007-06-01" name="birthDate" required>
            </div>

            <div class="signup-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="28123.luispinto@escolaaugustogomes.pt" name="email" placeholder="Escreva o seu email" required>
            </div>

            <div class="signup-group">
                <label for="password">Senha</label>
                <div class="signup-password-box">
                    <input type="password" id="password" value="1234" name="password" placeholder="Escreva a sua senha" required>
                    <span class="toggle-view" onclick="togglePasswordVisibility('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="signup-group">
                <label for="confirm-password">Confirme a Senha</label>
                <div class="signup-password-box">
                    <input type="password" id="confirm-password" value="1234" name="confirm-password" placeholder="Confirme a sua senha" required>
                    <span class="toggle-view" onclick="togglePasswordVisibility('confirm-password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="signup-group">
                <label for="resume">Currículo (Apenas PDF)</label>
                <input type="file" id="resume" name="resume" accept=".pdf" required>
            </div>

            <button type="submit" class="signup-submit">Registar</button>
        </form>

        <div class="signup-footer">
            Já tem uma conta? <a href="formlogin.php">Entrar</a>
        </div>
    </div>
</section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePasswordVisibility(id) {
        var passwordInput = document.getElementById(id);
        var eyeIcon = passwordInput.nextElementSibling.querySelector("i");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
    // Fechar automaticamente o toast após 4 segundos
    window.onload = function() {
        let toast = document.getElementById("toast-message");
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = "0";
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }
    };
    </script>

    <?php require "assets/elements/footer.php"; ?>
</body>
</html>
