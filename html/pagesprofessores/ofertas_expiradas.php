<?php
session_start();
include '../database/mysqli.php'; // Conexão com o banco

// Garante que o usuário logado seja um professor
if (!isset($_SESSION['professor_id'])) {
    header("Location: ../formlogin.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

// Buscar o curso do professor logado
$sql_curso = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmt_curso = $conn->prepare($sql_curso);
$stmt_curso->bind_param("i", $professor_id);
$stmt_curso->execute();
$result_curso = $stmt_curso->get_result();
$curso = $result_curso->fetch_assoc();
$id_curso_professor = $curso['id_curso'];

// Buscar ofertas **expiradas** do curso do professor
$sql_ofertas = "SELECT id_oferta, titulo, descricao, data_fim 
                FROM ofertas_empresas 
                WHERE id_curso = ? AND data_fim < NOW()
                ORDER BY data_fim DESC";

$stmt = $conn->prepare($sql_ofertas);
$stmt->bind_param("i", $id_curso_professor);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas Expiradas - Professor</title>
    <link rel="stylesheet" href="../assets/css/allcss.css"> <!-- Se necessário -->
</head>
<body>

    <div class="ofertas-expiradas-container">
        <h2 class="title">Ofertas Expiradas do Seu Curso</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="ofertas-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Data de Expiração</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['titulo']); ?></td>
                            <td><?= htmlspecialchars($row['descricao']); ?></td>
                            <td><?= date("d/m/Y", strtotime($row['data_fim'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">Nenhuma oferta expirada encontrada para o seu curso.</p>
        <?php endif; ?>
    </div>

    <style>
        .ofertas-expiradas-container {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .title {
            font-size: 2rem;
            color: white;
            margin-bottom: 20px;
        }

        .ofertas-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .ofertas-table th, .ofertas-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .ofertas-table th {
            background: #4f8cff;
            color: white;
        }

        .no-results {
            color: white;
            font-size: 1.2rem;
            margin-top: 20px;
        }
    </style>

</body>
</html>
