<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'C:/xampp/htdocs/pap/database/mysqli.php'; // Certifique-se de que o caminho está correto

// Verificar se a conexão com o banco de dados foi estabelecida corretamente
if (!$conn) {
    die("Erro: A conexão com o banco de dados não foi estabelecida.");
}

// Verifica se o usuário está logado e é um professor
if (!isset($_SESSION['id_professor'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}

$id_professor = $_SESSION['id_professor'];

// Buscar o curso do professor logado
$sql_curso = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $id_professor);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();

if ($result_curso->num_rows === 0) {
    die("Erro: Não foi possível encontrar o curso do professor.");
}

$row_curso = $result_curso->fetch_assoc();
$id_curso = $row_curso['id_curso'];

// Buscar todas as ofertas **expiradas** do curso do professor
$sql_ofertas = "SELECT oe.id_oferta, oe.titulo, oe.descricao, oe.vagas, oe.data_inicio, oe.data_fim, 
                       c.nome AS curso_relacionado, e.nome_empresa 
                FROM ofertas_empresas oe
                INNER JOIN empresas e ON oe.id_empresa = e.id_empresas
                INNER JOIN cursos c ON oe.id_curso = c.id_curso
                WHERE oe.data_fim < CURDATE() AND oe.id_curso = ?
                ORDER BY oe.data_fim DESC";

$stmt = $conn->prepare($sql_ofertas);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas Expiradas</title>
    <link rel="stylesheet" href="../assets/css/allcss.css"> <!-- Certifica-te de que o caminho está correto -->
</head>
<body>

<div class="expired-offers-container">
    <h2 class="expired-offers-title">Ofertas Expiradas</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="expired-offers-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="expired-offer-card">
                    <h3 class="expired-offer-title"><?= htmlspecialchars($row['titulo']); ?></h3>
                    <p class="expired-offer-detail"><strong>Empresa:</strong> <?= htmlspecialchars($row['nome_empresa']); ?></p>
                    <p class="expired-offer-detail"><strong>Descrição:</strong> <?= htmlspecialchars($row['descricao']); ?></p>
                    <p class="expired-offer-detail"><strong>Vagas:</strong> <?= htmlspecialchars($row['vagas']); ?></p>
                    <p class="expired-offer-detail"><strong>Curso Relacionado:</strong> <?= htmlspecialchars($row['curso_relacionado']); ?></p>
                    <p class="expired-offer-dates"><strong>Início:</strong> <?= date("d/m/Y", strtotime($row['data_inicio'])); ?> | <strong>Fim:</strong> <?= date("d/m/Y", strtotime($row['data_fim'])); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="expired-offer-detail">Nenhuma oferta expirada encontrada para o seu curso.</p>
    <?php endif; ?>
</div>
        
</body>
</html>
