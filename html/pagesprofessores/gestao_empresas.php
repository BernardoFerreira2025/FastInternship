<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

// Verifica se o professor está autenticado
if (!isset($_SESSION['id_professor']) || $_SESSION['user_role'] !== 'professor') {
    header("Location: ../formlogin.php");
    exit();
}

$id_curso = $_SESSION['id_curso'] ?? null;

if (!$id_curso) {
    echo "<p style='color:red;'>Erro: ID do curso não está definido.</p>";
    exit();
}

// Mensagem de pop-up (toast)
$toast_message = "";
$toast_class = "toast-success";

if (isset($_SESSION['toast_message'])) {
    $toast_message = $_SESSION['toast_message'];
    if (stripos($toast_message, 'erro') !== false || stripos($toast_message, 'exclu') !== false) {
        $toast_class = "toast-error";
    }
    unset($_SESSION['toast_message']);
}

// Buscar apenas as empresas do curso do professor
$stmt = $conn->prepare("SELECT id_empresas, nome_empresa, email, telefone, responsavel, morada, cod_postal, Localidade, foto FROM empresas WHERE id_curso = ?");
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$empresas = $stmt->get_result();
?>

<?php if (!empty($toast_message)) { ?>
    <div id="toast" class="toast-message <?= $toast_class ?>">
        <?= htmlspecialchars($toast_message) ?>
    </div>
<?php } ?>

<div class="users-container">
    <h2 class="users-header">Empresas</h2>

    <div class="users-grid">
        <?php if ($empresas->num_rows === 0): ?>
            <p style="color: yellow;">Nenhuma empresa encontrada para o teu curso.</p>
        <?php endif; ?>

        <?php while ($empresa = $empresas->fetch_assoc()) { ?>
            <div class="user-card">
                <div class="profile-pic-container">
                    <img src="<?= !empty($empresa['foto']) ? '../images/' . $empresa['foto'] : '../images/company_default.png' ?>" alt="Foto da Empresa">
                </div>
                <h3><?= htmlspecialchars($empresa['nome_empresa']) ?></h3>
                <p><i class="fas fa-user-tie"></i> <strong>Responsável:</strong> <?= htmlspecialchars($empresa['responsavel']) ?></p>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($empresa['email']) ?></p>
                <p><i class="fas fa-phone"></i> <strong>Telefone:</strong> <?= htmlspecialchars($empresa['telefone']) ?></p>
                <p><i class="fas fa-map-marked-alt"></i> <strong>Morada:</strong> <?= htmlspecialchars($empresa['morada']) ?></p>
                <p><i class="fas fa-mail-bulk"></i> <strong>Código Postal:</strong> <?= htmlspecialchars($empresa['cod_postal']) ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <strong>Localidade:</strong> <?= htmlspecialchars($empresa['Localidade']) ?></p>
                <div class="user-actions">
                    <a href='professor_dashboard.php?page=editar_empresa&id=<?= $empresa['id_empresas'] ?>' class="edit"><i class="fas fa-pen-to-square action-icon"></i> Editar</a>
                    <a href='pagesprofessores/excluir_empresa.php?id=<?= $empresa['id_empresas'] ?>' class="delete"><i class="fas fa-trash action-icon"></i> Excluir</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let toast = document.getElementById("toast");
        if (toast) {
            setTimeout(() => toast.style.display = "none", 4000);
        }
    });
</script>
