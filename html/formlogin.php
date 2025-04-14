<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FastInternship</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php require 'assets/elements/header.php'; ?>

    <!-- Exibir toast caso a senha esteja incorreta -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="toast-message toast-error" id="toast-message">
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); // Remove o erro após exibir ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['toast_message'])): ?>
        <div class="toast-message toast-success" id="toast-message">
            <?php echo $_SESSION['toast_message']; ?>
        </div>
        <?php unset($_SESSION['toast_message']); // Remove o erro após exibir ?>
    <?php endif; ?>

    <!-- Login Section -->
    <main class="auth-container d-flex justify-content-center align-items-center">
        <div class="auth-card">
            <h2 class="text-center">Faça o seu login</h2>
            <p class="text-center">Insira os seus dados para entrar</p>
            
            <form action="loginhandler.php" method="POST">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Insira o seu e-mail" 
                    value="<?php echo isset($_SESSION['email_temp']) ? $_SESSION['email_temp'] : ''; ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Insira a sua senha" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="forgot-password text-end">
                    <a href="recuperacao/reset_password.php">Esqueceu-se da palavra-passe?</a>
                </div>
                <button type="submit" class="login-btn">Entrar</button>
            </form>

            <div class="signup-link text-center mt-3">
                <p>Não tem uma conta? <a href="signup.php">Registe-se</a></p>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para alternar a exibição da senha -->
    <script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("password");
        var eyeIcon = document.querySelector(".toggle-password i");

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

    <?php include 'assets/elements/footer.php'; ?>
</body>
</html>
