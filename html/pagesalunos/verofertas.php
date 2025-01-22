<?php
require_once '../database/mysqli.php';

if (!$conn) {
    die("Falha ao conectar com a base de dados: " . mysqli_connect_error());
}

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
</head>
<body>
<?php
// Verifica se a candidatura foi bem-sucedida e exibe o toast
if (isset($_GET['candidatura']) && $_GET['candidatura'] === 'sucesso') {
    echo "<script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Candidatura enviada com sucesso!',
            showConfirmButton: false,
            timer: 3000
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
                <a href="aluno_dashboard.php?page=candidatar&id=<?php echo urlencode($oferta['id_oferta']); ?>" class="btn-view">Candidatar</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Não há ofertas disponíveis no momento.</p>
    <?php endif; ?>
</div>
</body>
</html>
