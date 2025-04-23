<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../database/mysqli.php'; // Caminho correto para o banco de dados

// Verifica se o usuário está logado e é um professor
if (!isset($_SESSION['id_professor'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}

$id_professor = $_SESSION['id_professor'];

// Obtém o curso do professor logado
$query = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_professor);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();
$id_curso = $professor['id_curso'] ?? null;

if (!$id_curso) {
    die("<p>Erro: O professor não tem um curso associado.</p>");
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_empresa = $conn->real_escape_string($_POST['nome_empresa']);
    $responsavel = $conn->real_escape_string($_POST['responsavel']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefone = $conn->real_escape_string($_POST['telefone']);
    $morada = $conn->real_escape_string($_POST['morada']);
    $cod_postal = $conn->real_escape_string($_POST['cod_postal']);
    $localidade = $conn->real_escape_string($_POST['localidade']);
    $senha = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encripta a senha

    // Validação no Backend (PHP)
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
        $_SESSION['toast_message'] = "Apenas e-mails @gmail.com são permitidos.";
        $_SESSION['toast_type'] = "error";
    } elseif (!preg_match('/^\d{3}-\d{3}-\d{3}$/', $telefone)) {
        $_SESSION['toast_message'] = "O telefone deve estar no formato XXX-XXX-XXX.";
        $_SESSION['toast_type'] = "error";
    } elseif (!preg_match('/^\d{4}-\d{3}$/', $cod_postal)) {
        $_SESSION['toast_message'] = "O código postal deve estar no formato XXXX-XXX.";
        $_SESSION['toast_type'] = "error";
    } else {
        // Insere os dados na tabela `empresas`
        $sql = "INSERT INTO empresas (nome_empresa, responsavel, email, telefone, morada, cod_postal, Localidade, Password, id_curso) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $nome_empresa, $responsavel, $email, $telefone, $morada, $cod_postal, $localidade, $senha, $id_curso);

        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Empresa adicionada com sucesso!";
            $_SESSION['toast_type'] = "success";
            header("Location: professor_dashboard.php?page=gestao_empresas");
            exit();
        } else {
            $_SESSION['toast_message'] = "Erro ao adicionar empresa.";
            $_SESSION['toast_type'] = "error";
        }
    }
}
?>

<!-- Estrutura do formulário -->
<div class="form-background">
    <div class="form-wrapper">
        <h1 class="users-header">Adicionar Empresa</h1>

        <form method="POST" id="empresaForm">
        <label>Nome da Empresa:</label>
<input type="text" name="nome_empresa" required value="FedEx">

<label>Responsável:</label>
<input type="text" name="responsavel" required value="Joaquim Sousa">

<label>Email:</label>
<input type="email" id="email" name="email" required value="fedex@gmail.com">

<label>Telefone:</label>
<input type="text" id="telefone" name="telefone" required placeholder="XXX-XXX-XXX" maxlength="11" value="911-234-642">

<label>Morada:</label>
<input type="text" name="morada" required value="Rua Engenheiro Ferreira Dias, 924">

<label>Código Postal:</label>
<input type="text" id="cod_postal" name="cod_postal" required placeholder="XXXX-XXX" maxlength="8" value="4100-247">

<label>Localidade:</label>
<input type="text" name="localidade" required value="Porto">

            <label>Senha:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i> <!-- Ícone do olho -->
            </div>


            <!-- Curso automático baseado no professor logado -->
            <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">

            <button type="submit" class="btn-submit">Adicionar Empresa</button>
        </form>
    </div>
</div>

<!-- Scripts para Exibir Toasts e Validação -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Exibir toast se existir mensagem na sessão
        let toastMessage = "<?php echo $_SESSION['toast_message'] ?? ''; ?>";
        let toastType = "<?php echo $_SESSION['toast_type'] ?? ''; ?>";

        if (toastMessage) {
            showToast(toastMessage, toastType);
            <?php unset($_SESSION['toast_message'], $_SESSION['toast_type']); ?>
        }

        // Validação no frontend
        document.getElementById("empresaForm").addEventListener("submit", function(event) {
            let emailInput = document.getElementById("email").value;
            let telefoneInput = document.getElementById("telefone").value;
            let codPostalInput = document.getElementById("cod_postal").value;

            if (!emailInput.endsWith("@gmail.com")) {
                showToast("Apenas e-mails @gmail.com são permitidos.", "error");
                event.preventDefault();
                return;
            }

            if (!/^\d{3}-\d{3}-\d{3}$/.test(telefoneInput)) {
                showToast("O telefone deve estar no formato XXX-XXX-XXX.", "error");
                event.preventDefault();
                return;
            }

            if (!/^\d{4}-\d{3}$/.test(codPostalInput)) {
                showToast("O código postal deve estar no formato XXXX-XXX.", "error");
                event.preventDefault();
                return;
            }
        });

        // Formatação dinâmica dos campos
        document.getElementById("telefone").addEventListener("input", function() {
            this.value = this.value.replace(/\D/g, "").slice(0, 9).replace(/(\d{3})(\d{3})(\d{3})/, "$1-$2-$3");
        });

        document.getElementById("cod_postal").addEventListener("input", function() {
            this.value = this.value.replace(/\D/g, "").slice(0, 7).replace(/(\d{4})(\d{3})/, "$1-$2");
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
    });

    // Função para exibir toast dinâmico
    function showToast(message, type) {
        let toast = document.createElement("div");
        toast.className = `toast-message toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
</script>
