<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar-se - FastInternship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
</head>
<body>
    <?php require "assets/elements/header.php"; ?>

    <div class="signup-container">
        <div class="signup-card">
            <h2 class="gradient-title">Crie a Sua Conta</h2>
            <p>Crie a sua conta para aceder às melhores oportunidades de estágio</p>
            <form id="signupForm" action="signuphandler.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Escreva o seu nome completo" required>
                </div>
                <div class="form-group">
                    <label for="processNumber">Número de Processo</label>
                    <input type="text" id="processNumber" name="processNumber" class="form-input" placeholder="Escreva o seu número de processo" required>
                </div>
                <div class="form-group">
                    <label for="class">Turma</label>
                    <input type="text" id="class" name="class" class="form-input" placeholder="Escreva a sua turma" required>
                </div>
                <div class="form-group">
                    <label for="course">Curso</label>
                    <select id="course" name="course" class="form-input" required>
                        <option value="" disabled selected>Selecione o seu curso</option>
                        <option value="Técnico(a) de Gestão e Programação de Sistemas Informáticos">Técnico(a) de Gestão e Programação de Sistemas Informáticos</option>
                        <option value="Técnico(a) de Turismo">Técnico(a) de Turismo</option>
                        <option value="Técnico(a) de Multimédia">Técnico(a) de Multimédia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birthDate">Data de Nascimento</label>
                    <input type="date" id="birthDate" name="birthDate" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Escreva o seu email" required>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Escreva a sua senha" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirme a Senha</label>
                    <div class="password-container">
                        <input type="password" id="confirm-password" name="confirm-password" class="form-input" placeholder="Confirme a sua senha" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="resume">Currículo (Apenas PDF)</label>
                    <input type="file" id="resume" name="resume" class="form-input" accept=".pdf" required>
                </div>
                <button type="submit" class="signup-btn">Registar</button>
            </form>

            <div class="login-link">
                Já tem uma conta? <a href="formlogin.php">Entrar</a>
            </div>
        </div>
    </div>

    <?php require "assets/elements/footer.php"; ?>

    <script src="assets/js/signup.js"></script>
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
    </script>
</body>
</html>
