<?php
require_once 'C:/xampp/htdocs/pap/database/mysqli.php';

// Verificar se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar conexão com o banco de dados
if (!$conn) {
    die("Falha ao conectar com a base de dados: " . mysqli_connect_error());
}

$id_aluno = $_SESSION['id_aluno'] ?? null;

if (!$id_aluno) {
    die("Você precisa estar autenticado para visualizar esta página.");
}

// Verificar número de candidaturas do aluno
$sql_count_candidaturas = "SELECT COUNT(*) AS total_candidaturas FROM candidaturas WHERE id_aluno = ?";
$stmt = $conn->prepare($sql_count_candidaturas);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$stmt->bind_result($total_candidaturas);
$stmt->fetch();
$stmt->close();

// Obter ofertas disponíveis
$sql_ofertas = "SELECT o.*, e.nome_empresa as empresa_nome, e.responsavel as empresa_responsavel
                FROM ofertas_empresas o 
                INNER JOIN empresas e ON o.id_empresa = e.id_empresas";

$result_ofertas = $conn->query($sql_ofertas);

if ($result_ofertas) {
    $ofertas = $result_ofertas->fetch_all(MYSQLI_ASSOC);
} else {
    error_log("Erro na query SQL: " . $conn->error);
    die("Erro ao carregar ofertas.");
}

// Fechar conexão após obter os resultados
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Ofertas Disponíveis</title>
    <style>
        .swal2-popup {
            font-family: 'Arial', sans-serif; /* Defina a fonte aqui */
        }
    </style>
</head>
<body>
<?php
// Exibir o pop-up de sucesso da candidatura
if (isset($_GET['candidatura']) && $_GET['candidatura'] === 'sucesso') {
    echo "<script>
        Swal.fire({
            title: 'Sucesso!',
            text: 'Candidatura enviada com sucesso!',
            icon: 'success',
            confirmButtonColor: '#4CAF50',
            timer: 3000,
            showConfirmButton: false
        });
    </script>";
}

// Verificar se o limite de candidaturas foi atingido
if ($total_candidaturas >= 3) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Limite de Candidaturas Atingido',
            text: 'Você só pode se candidatar a no máximo 3 ofertas.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33',
            
        });
    </script>";
}

// Exibir o pop-up de sucesso da candidatura cancelada
if (isset($_GET['candidatura']) && $_GET['candidatura'] === 'cancelada') {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Candidatura cancelada com sucesso!',
            confirmButtonColor: '#4CAF50'
        });
    </script>";
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
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($oferta['descricao']); ?></p>
                <p><strong>Data de Início:</strong> <?php echo htmlspecialchars($oferta['data_inicio']); ?></p>
                <p><strong>Data de Fim:</strong> <?php echo htmlspecialchars($oferta['data_fim']); ?></p>
                <p><strong>Requisitos:</strong> <?php echo htmlspecialchars($oferta['requisitos']); ?></p>
                <p><strong>Vagas:</strong> <?php echo htmlspecialchars($oferta['vagas']); ?></p>
                <p><strong>Curso Relacionado:</strong> <?php echo htmlspecialchars($oferta['curso_relacionado']); ?></p>
                <?php if ($total_candidaturas < 3): ?>
                    <a href="aluno_dashboard.php?page=candidatar&id=<?php echo urlencode($oferta['id_oferta']); ?>" class="btn-view">Candidatar</a>
                <?php else: ?>
                    <button class="btn-disabled" onclick="limiteCandidaturas()" disabled>Limite Atingido</button>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Não há ofertas disponíveis no momento.</p>
    <?php endif; ?>
</div>
</body>
</html>