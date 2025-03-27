<?php
session_start(); // Inicia sessão para verificar o status do e-mail
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacte-nos - FastInternship</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css">
    <link rel="stylesheet" href="assets/elements/footer.css">
</head>
<body>
        <?php include 'assets/elements/header.php'; ?>
    <div class="contact-container">
        <div class="contact-particles"></div>

        <div class="contact-wrapper">
            <div class="contact-info">
                <div class="info-header">
                    <h2>Contacte-nos</h2>
                    <p>Entre em contacto connosco através de qualquer uma destas plataformas</p>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <h3>Localização</h3>
                        <p>Tv. Silva Pinheiro, Matosinhos</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <h3>E-mail</h3>
                        <p>fastinternship@gmail.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <h3>Telefone</h3>
                        <p>+351 915 008 662</p>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" class="social-link">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                </div>
            </div>

            <!-- Formulário de Contacto -->
            <div class="contact-form">
                <div class="form-header">
                    <h2>Envie-nos uma mensagem</h2>
                    <p>Tem dúvidas, sugestões ou precisa de ajuda? Preencha o formulário abaixo e entraremos em contacto consigo o mais breve possível.</p>
                </div>

                <form id="contact-form" action="enviar_email.php" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="name">Nome</label>
                        <input type="text" name="name" id="name" class="form-input" placeholder="Insira o seu nome" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">O Seu E-mail</label>
                        <input type="email" name="email" id="email" class="form-input" placeholder="Insira o seu e-mail" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="subject">Assunto</label>
                        <input type="text" name="subject" id="subject" class="form-input" placeholder="Insira o assunto" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">A Sua Mensagem</label>
                        <textarea name="message" id="message" class="form-input" rows="5" placeholder="Escreva a sua mensagem" required></textarea>
                    </div>

                    <button type="submit" class="signup-btn">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </div>
        <?php include 'assets/elements/footer.php'; ?>
    <!-- Toast Notification -->
    <div id="toast" class="toast-message" style="display: none;"></div>

    <!-- JavaScript para exibir Toast -->
    <script>
        function showToast(message, type) {
            var toast = document.getElementById("toast");
            toast.innerText = message;
            toast.className = "toast-message " + (type === "error" ? "toast-error" : "toast-success");

            toast.style.display = "block";
            setTimeout(function () {
                toast.style.display = "none";
            }, 4000);
        }

        // Verifica se há uma mensagem de status na sessão e exibe o toast
        <?php if (isset($_SESSION['email_status'])): 
            list($type, $msg) = explode('|', $_SESSION['email_status']);
            unset($_SESSION['email_status']); // Limpa a sessão após exibição ?>
            showToast("<?= $msg ?>", "<?= $type ?>");
        <?php endif; ?>
    </script>

</body>
</html>
