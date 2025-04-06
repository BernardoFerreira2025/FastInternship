<?php
// Conexão com o banco de dados
require_once '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o professor está logado e obtém o ID do curso ao qual ele pertence
if (!isset($_SESSION['id_professor'])) {
    die("Acesso negado.");
}

$id_professor = $_SESSION['id_professor'];

// Busca o id_curso do professor logado
$queryCurso = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmtCurso = $conn->prepare($queryCurso);
$stmtCurso->bind_param("i", $id_professor);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();
$curso = $resultCurso->fetch_assoc();

if (!$curso) {
    die("Erro ao obter curso do professor.");
}

$id_curso = $curso['id_curso'];

// Consulta para obter as ofertas do curso do professor logado
$query = "SELECT oe.id_oferta, oe.titulo, oe.descricao, oe.vagas, oe.id_curso, oe.data_inicio, oe.data_fim, 
                 (SELECT COUNT(*) FROM candidaturas c WHERE c.id_oferta = oe.id_oferta AND c.status_professor = 'aprovado') AS candidaturas_aprovadas
          FROM ofertas_empresas oe
          WHERE oe.id_curso = ? AND oe.data_fim >= CURDATE()";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="form-background">
    <div class="form-wrapper">
        <h1 class="users-header">Controlo das Candidaturas</h1>

        <div class="users-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='offer-card'>";
                    echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
                    echo "<p><strong>Descrição:</strong> " . htmlspecialchars($row['descricao']) . "</p>";
                    echo "<p><strong>Vagas:</strong> " . htmlspecialchars($row['vagas']) . "</p>";
                    echo "<p><strong>Candidaturas Aprovadas:</strong> " . htmlspecialchars($row['candidaturas_aprovadas']) . "</p>";

                    // Obter o nome do curso
                    $cursoNomeQuery = "SELECT nome FROM cursos WHERE id_curso = ?";
                    $stmtCursoNome = $conn->prepare($cursoNomeQuery);
                    $stmtCursoNome->bind_param("i", $row['id_curso']);
                    $stmtCursoNome->execute();
                    $cursoNomeResult = $stmtCursoNome->get_result();
                    $cursoNome = $cursoNomeResult->fetch_assoc()['nome'] ?? 'Curso não encontrado';

                    echo "<p><strong>Curso Relacionado:</strong> " . htmlspecialchars($cursoNome) . "</p>";
                    echo "<p><strong>Início:</strong> " . htmlspecialchars($row['data_inicio']) . "</p>";
                    echo "<p><strong>Fim:</strong> " . htmlspecialchars($row['data_fim']) . "</p>";
                    echo "<a href='professor_dashboard.php?page=alunos_candidatos&oferta_id=" . $row['id_oferta'] . "' class='btn-view'>Ver Candidatos</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>Não existe nenhuma candidatura.</p>";
            }
            ?>
        </div>
    </div>
</div>
