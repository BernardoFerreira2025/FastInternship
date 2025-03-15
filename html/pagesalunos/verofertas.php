<?php
require_once 'C:/xampp/htdocs/pap/database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o aluno está logado
if (!isset($_SESSION['id_aluno'])) {
    die("Você precisa estar autenticado para visualizar esta página.");
}

$id_aluno = $_SESSION['id_aluno'];

// Obter o ID do curso do aluno logado
$sql_curso_aluno = "SELECT id_curso FROM alunos WHERE id_aluno = ?";
$stmt = $conn->prepare($sql_curso_aluno);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$stmt->bind_result($id_curso_aluno);
$stmt->fetch();
$stmt->close();

// Contar candidaturas do aluno
$sql_count_candidaturas = "SELECT COUNT(*) AS total_candidaturas FROM candidaturas WHERE id_aluno = ?";
$stmt = $conn->prepare($sql_count_candidaturas);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$stmt->bind_result($total_candidaturas);
$stmt->fetch();
$stmt->close();

// Obter ofertas disponíveis APENAS do curso do aluno e DENTRO DO PRAZO
$sql_ofertas = "SELECT o.*, e.nome_empresa AS empresa_nome, e.responsavel AS empresa_responsavel, 
                       c.nome AS curso_relacionado,
                       (SELECT COUNT(*) FROM candidaturas WHERE id_aluno = ? AND id_oferta = o.id_oferta) AS ja_candidatado
                FROM ofertas_empresas o 
                INNER JOIN empresas e ON o.id_empresa = e.id_empresas
                INNER JOIN cursos c ON o.id_curso = c.id_curso
                WHERE o.id_curso = ? 
                AND o.data_fim >= CURDATE()  -- 📌 Filtrar apenas ofertas dentro do prazo
                AND o.vagas > 0";  // 📌 Garantir que a oferta ainda tem vagas

$stmt = $conn->prepare($sql_ofertas);
$stmt->bind_param("ii", $id_aluno, $id_curso_aluno);
$stmt->execute();
$result_ofertas = $stmt->get_result();
$ofertas = $result_ofertas->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Ofertas Disponíveis</title>
</head>
<body>

<?php
// Exibir pop-ups com base nas ações
if (isset($_GET['candidatura'])) {
    if ($_GET['candidatura'] === 'sucesso') {
        echo "<script>Swal.fire({ title: 'Sucesso!', text: 'Candidatura enviada com sucesso!', icon: 'success', confirmButtonColor: '#4CAF50', timer: 3000, showConfirmButton: false });</script>";
    } elseif ($_GET['candidatura'] === 'cancelada') {
        echo "<script>Swal.fire({ icon: 'success', title: 'Candidatura cancelada com sucesso!', confirmButtonColor: '#4CAF50' });</script>";
    }
}

// Verificar se o limite de candidaturas foi atingido
if ($total_candidaturas >= 3) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Limite de Candidaturas Atingido', text: 'Você só pode se candidatar a no máximo 3 ofertas.', confirmButtonText: 'Entendido', confirmButtonColor: '#d33' });</script>";
}
?>

<h1>Ofertas Disponíveis</h1>
<div class="offers-section">
    <?php if (!empty($ofertas)): ?>
        <?php foreach ($ofertas as $oferta): ?>
            <div class="offer-card">
                <h3><?php echo htmlspecialchars($oferta['titulo']); ?></h3>
                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($oferta['empresa_nome']); ?></p>
                <p><strong>Responsável:</strong> <?php echo htmlspecialchars($oferta['empresa_responsavel']); ?></p>
                <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($oferta['descricao'])); ?></p>
                <p><strong>Período:</strong> <?php echo htmlspecialchars($oferta['data_inicio']) . " a " . htmlspecialchars($oferta['data_fim']); ?></p>
                <p><strong>Vagas Disponíveis:</strong> <?php echo htmlspecialchars($oferta['vagas']); ?></p>

                <p><strong>Curso Relacionado:</strong> 
                    <?php echo !empty($oferta['curso_relacionado']) ? htmlspecialchars($oferta['curso_relacionado']) : 'Não informado'; ?>
                </p>

                <?php if ($oferta['ja_candidatado'] > 0): ?>
                    <button class="btn btn-danger btn-remove-candidatura" disabled>Já Candidatado</button>
                <?php elseif ($total_candidaturas < 3): ?>
                    <a href="aluno_dashboard.php?page=candidatar&id=<?php echo urlencode($oferta['id_oferta']); ?>" class="btn-view">Candidatar</a>
                <?php else: ?>
                    <button class="btn-disabled" onclick="limiteCandidaturas()" disabled>Limite Atingido</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-data">Nenhuma oferta disponível no momento para o seu curso.</p>
    <?php endif; ?>
</div>

</body>
</html>
