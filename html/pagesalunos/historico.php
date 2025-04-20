<?php
require_once '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_aluno = $_SESSION['id_aluno'] ?? null;

if (!$id_aluno) {
    die("Você precisa estar autenticado para visualizar esta página.");
}

$toastMessage = null;
if (isset($_SESSION['toastMessage'])) {
    $toastMessage = $_SESSION['toastMessage'];
    unset($_SESSION['toastMessage']);
}

// Exibir apenas candidaturas cujo status do professor NÃO seja "rejeitado"
$sql_historico = "SELECT c.id_candidatura, o.titulo, o.descricao, o.data_inicio, o.data_fim, 
                         e.nome_empresa, c.status_professor, c.status_empresa
                  FROM candidaturas c
                  INNER JOIN ofertas_empresas o ON c.id_oferta = o.id_oferta
                  INNER JOIN empresas e ON o.id_empresa = e.id_empresas
                  WHERE c.id_aluno = ? 
                  AND c.status_professor != 'rejeitado'";
$stmt = $conn->prepare($sql_historico);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();
$candidaturas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_candidatura'])) {
    $id_candidatura = $_POST['id_candidatura'];

    $sql_delete = "DELETE FROM candidaturas WHERE id_candidatura = ? AND id_aluno = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $id_candidatura, $id_aluno);

    if ($stmt->execute()) {
        $_SESSION['toastMessage'] = [
            'type' => 'success',
            'title' => 'Candidatura cancelada com sucesso!',
        ];
    } else {
        $_SESSION['toastMessage'] = [
            'type' => 'error',
            'title' => 'Erro ao cancelar a candidatura. Tente novamente.',
        ];
    }

    $stmt->close();

    header("Location: aluno_dashboard.php?page=verofertas");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Histórico de Candidaturas</title>
</head>
<body>
<div class="historico-container">
    <h2 class="historico-titulo">Histórico de Candidaturas</h2>
    <div class="historico-grid">
        <?php if (!empty($candidaturas)): ?>
            <?php foreach ($candidaturas as $candidatura): ?>
                <div class="historico-card">
                    <h3><?php echo htmlspecialchars($candidatura['titulo']); ?></h3>
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($candidatura['descricao']); ?></p>
                    <p><strong>Data de Início:</strong> <?php echo htmlspecialchars($candidatura['data_inicio']); ?></p>
                    <p><strong>Data de Fim:</strong> <?php echo htmlspecialchars($candidatura['data_fim']); ?></p>

                    <p><strong>Status do Professor:</strong> 
                        <span class="status-<?php echo htmlspecialchars(strtolower($candidatura['status_professor'])); ?>">
                            <?php echo htmlspecialchars($candidatura['status_professor']); ?>
                        </span>
                    </p>

                    <p><strong>Status da Empresa:</strong> 
                        <span class="status-<?php echo htmlspecialchars(strtolower($candidatura['status_empresa'])); ?>">
                            <?php echo htmlspecialchars($candidatura['status_empresa']); ?>
                        </span>
                    </p>

                    <?php
                    $statusProfessor = strtolower($candidatura['status_professor']);
                    $statusEmpresa = strtolower($candidatura['status_empresa']);
                    if ($statusProfessor === 'pendente' && $statusEmpresa === 'pendente'):
                    ?>
                        <form method="post" onsubmit="return confirmCancel(event, this)">
                            <input type="hidden" name="id_candidatura" value="<?php echo $candidatura['id_candidatura']; ?>">
                            <button type="submit" class="btn-cancelar-historico">Cancelar Candidatura</button>
                        </form>
                    <?php else: ?>
                        <p class="historico-avaliada-msg">A candidatura já foi avaliada por uma das partes responsáveis.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Você ainda não se candidatou a nenhuma oferta.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmCancel(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja realmente cancelar esta candidatura?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF4F4F',
            cancelButtonColor: '#4CAF50',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
</body>
</html>