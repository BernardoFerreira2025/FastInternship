<?php
require_once '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_aluno = $_SESSION['id_aluno'] ?? null;
if (!$id_aluno) {
    die("Acesso não autorizado.");
}

// Buscar candidaturas analisadas por ambas as partes
$sql = "SELECT c.id_candidatura, o.titulo, o.descricao, o.data_inicio, o.data_fim, 
               e.nome_empresa, c.status_professor, c.status_empresa
        FROM candidaturas c
        INNER JOIN ofertas_empresas o ON c.id_oferta = o.id_oferta
        INNER JOIN empresas e ON o.id_empresa = e.id_empresas
        WHERE c.id_aluno = ? 
          AND c.status_professor != 'pendente' 
          AND c.status_empresa != 'pendente'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();
$candidaturas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Separar candidaturas
$aprovadas = [];
$rejeitadas = [];

foreach ($candidaturas as $candidatura) {
    $profStatus = strtolower($candidatura['status_professor']);
    $empStatus = strtolower($candidatura['status_empresa']);

    if ($profStatus === 'aprovado' && $empStatus === 'aprovado') {
        $aprovadas[] = $candidatura;
    } else {
        $rejeitadas[] = $candidatura;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Candidaturas Analisadas</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <script>
    function showSection(tipo) {
        document.getElementById("secao-aprovadas").style.display = "none";
        document.getElementById("secao-rejeitadas").style.display = "none";

        const botoes = document.querySelectorAll(".filtro-opcao");
        botoes.forEach(btn => btn.classList.remove("ativo"));

        if (tipo === 'aprovadas') {
            document.getElementById("secao-aprovadas").style.display = "block";
            botoes[0].classList.add("ativo");
        } else {
            document.getElementById("secao-rejeitadas").style.display = "block";
            botoes[1].classList.add("ativo");
        }
    }

    window.onload = function () {
        showSection('aprovadas');
    };
    </script>
</head>
<body>

<div class="users-container">
    <h2 class="users-header">Candidaturas Analisadas</h2>

    <div class="filtro-toggle">
        <button class="filtro-opcao ativo" onclick="showSection('aprovadas')">
            <i class="fas fa-check-circle"></i> Aprovadas
        </button>
        <button class="filtro-opcao" onclick="showSection('rejeitadas')">
            <i class="fas fa-times-circle"></i> Rejeitadas
        </button>
    </div>

    <!-- Secção Aprovadas -->
    <div id="secao-aprovadas" class="users-grid">
        <?php if (count($aprovadas) > 0): ?>
            <?php foreach ($aprovadas as $c): ?>
                <div class="user-card">
                    <h3><?= htmlspecialchars($c['titulo']) ?></h3>
                    <p><strong>Empresa:</strong> <?= htmlspecialchars($c['nome_empresa']) ?></p>
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($c['descricao']) ?></p>
                    <p><strong>Data de Início:</strong> <?= htmlspecialchars($c['data_inicio']) ?></p>
                    <p><strong>Data de Fim:</strong> <?= htmlspecialchars($c['data_fim']) ?></p>
                    <p><strong>Status do Professor:</strong> 
                        <span class="status-aprovado"><?= htmlspecialchars($c['status_professor']) ?></span>
                    </p>
                    <p><strong>Status da Empresa:</strong> 
                        <span class="status-aprovado"><?= htmlspecialchars($c['status_empresa']) ?></span>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">Sem candidaturas aprovadas para mostrar.</p>
        <?php endif; ?>
    </div>

    <!-- Secção Rejeitadas -->
    <div id="secao-rejeitadas" class="users-grid" style="display: none;">
        <?php if (count($rejeitadas) > 0): ?>
            <?php foreach ($rejeitadas as $c): ?>
                <div class="user-card">
                    <h3><?= htmlspecialchars($c['titulo']) ?></h3>
                    <p><strong>Empresa:</strong> <?= htmlspecialchars($c['nome_empresa']) ?></p>
                    <p><strong>Descrição:</strong> <?= htmlspecialchars($c['descricao']) ?></p>
                    <p><strong>Data de Início:</strong> <?= htmlspecialchars($c['data_inicio']) ?></p>
                    <p><strong>Data de Fim:</strong> <?= htmlspecialchars($c['data_fim']) ?></p>
                    <p><strong>Status do Professor:</strong> 
                        <span class="status-<?= strtolower($c['status_professor']) ?>"><?= htmlspecialchars($c['status_professor']) ?></span>
                    </p>
                    <p><strong>Status da Empresa:</strong> 
                        <span class="status-<?= strtolower($c['status_empresa']) ?>"><?= htmlspecialchars($c['status_empresa']) ?></span>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">Sem candidaturas rejeitadas para mostrar.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
