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

<div class="oferta-container">
    <h2 class="oferta-titulo">Empresas</h2>

    <div class="oferta-grid">
        <?php if ($empresas->num_rows === 0): ?>
            <p style="color: yellow;">Nenhuma empresa encontrada para o teu curso.</p>
        <?php endif; ?>

        <?php while ($empresa = $empresas->fetch_assoc()) { ?>
            <div class="oferta-card">
                <div class="profile-pic-container" style="text-align: center; margin-bottom: 1rem;">
                    <img src="<?= !empty($empresa['foto']) ? '../images/' . $empresa['foto'] : '../images/company_default.png' ?>" alt="Foto da Empresa" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #fff;">
                </div>
                <h3><?= htmlspecialchars($empresa['nome_empresa']) ?></h3>
                <p><strong>Responsável:</strong> <?= htmlspecialchars($empresa['responsavel']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($empresa['email']) ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($empresa['telefone']) ?></p>
                <p><strong>Morada:</strong> <?= htmlspecialchars($empresa['morada']) ?></p>
                <p><strong>Código Postal:</strong> <?= htmlspecialchars($empresa['cod_postal']) ?></p>
                <p><strong>Localidade:</strong> <?= htmlspecialchars($empresa['Localidade']) ?></p>

                <div class="oferta-actions">
                    <a href='professor_dashboard.php?page=editar_empresa&id=<?= $empresa['id_empresas'] ?>' class="btn-editar">
                        <i class="fas fa-pen-to-square"></i> Editar
                    </a>
                    <a href='pagesprofessores/excluir_empresa.php?id=<?= $empresa['id_empresas'] ?>' class="btn-excluir">
                        <i class="fas fa-trash"></i> Excluir
                    </a>
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
