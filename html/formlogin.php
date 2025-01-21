<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FastInternship</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/allcss.css">
    <!-- CSS Global -->
    <link rel="stylesheet" href="assets/elements/header.css"> <!-- Certifique-se do caminho correto -->
    <link rel="stylesheet" href="assets/elements/footer.css">
    
</head>
<body>
        <?php require 'assets/elements/header.php'; ?>
    <!-- Header Section -->
    <div id="header"></div>

    <!-- Login Section -->
    <main class="auth-container d-flex justify-content-center align-items-center">
        <div class="auth-card">
            <h2 class="text-center">Faça o seu login</h2>
            <p class="text-center">Insira os seus dados para entrar</p>
            <form action="loginhandler.php" method="POST">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Insira o seu e-mail" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Insira a sua senha" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Lembrar-me</label>
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
    <?php include 'assets/elements/footer.php'; ?>
</body>
</html>
