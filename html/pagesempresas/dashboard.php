<?php
require_once '../database/mysqli.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a empresa está logada
if (!isset($_SESSION['id_empresas'])) {
    die("Acesso negado.");
}

$id_empresa = $_SESSION['id_empresas'];

// Busca as ofertas associadas a esta empresa
$query = "SELECT o.*, c.nome AS curso_nome
          FROM ofertas_empresas o
          INNER JOIN cursos c ON o.id_curso = c.id_curso
          WHERE o.id_empresa = ? AND o.data_fim >= CURDATE()";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_empresa);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="form-background">
    <div class="form-wrapper">
        <h1 class="users-header">Controlo das Candidaturas</h1>

        <div class="users-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="offer-card">
                        <h3><?= htmlspecialchars($row['titulo']) ?></h3>
                        <p><strong>Descrição:</strong> <?= htmlspecialchars($row['descricao']) ?></p>
                        <p><strong>Vagas:</strong> <?= htmlspecialchars($row['vagas']) ?></p>
                        <p><strong>Curso Relacionado:</strong> <?= $row['curso_nome'] === 'Técnico(a) de Gestão e Programação de Sistemas Informáticos' ? 'TGPSI' : htmlspecialchars($row['curso_nome']) ?></p>
                        <p><strong>Início:</strong> <?= htmlspecialchars($row['data_inicio']) ?></p>
                        <p><strong>Fim:</strong> <?= htmlspecialchars($row['data_fim']) ?></p>
                        <a href='empresa_dashboard.php?page=alunos_candidatos&oferta_id=<?= $row['id_oferta'] ?>' class='btn-view'>Ver Candidatos</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: yellow;">Nenhuma oferta publicada pela sua empresa.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Toasts
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.createElement('div');
            toast.className = 'toast-message toast-success';
            toast.textContent = '" . addslashes($_SESSION['mensagem_sucesso']) . "';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });
    </script>";
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.createElement('div');
            toast.className = 'toast-message toast-error';
            toast.textContent = '" . addslashes($_SESSION['mensagem_erro']) . "';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });
    </script>";
    unset($_SESSION['mensagem_erro']);
}
?>
