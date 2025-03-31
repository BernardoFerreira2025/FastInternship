<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

// Verifica se o ID foi enviado
if (isset($_GET['id'])) {
    $id_oferta = intval($_GET['id']); // Converte para inteiro

    // Busca os dados da oferta para edição
    $sql = "SELECT * FROM ofertas_empresas WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_oferta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $oferta = $resultado->fetch_assoc();
        $stmt->close();

        // Verifica se a oferta existe
        if (!$oferta) {
            $_SESSION['mensagem_sucesso'] = "Oferta não encontrada.";
            header("Location: gestao_ofertas.php");
            exit;
        }
    } else {
        $_SESSION['mensagem_sucesso'] = "Erro ao preparar a consulta.";
        header("Location: gestao_ofertas.php");
        exit;
    }
} else {
    $_SESSION['mensagem_sucesso'] = "ID da oferta não fornecido.";
    header("Location: gestao_ofertas.php");
    exit;
}

// Atualiza os dados da oferta no banco de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $vagas = intval($_POST['vagas']);

    $sql = "UPDATE ofertas_empresas SET titulo = ?, descricao = ?, data_inicio = ?, data_fim = ?, vagas = ? WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssii", $titulo, $descricao, $data_inicio, $data_fim, $vagas, $id_oferta);
        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Oferta alterada com sucesso!";
            header("Location: admin_dashboard.php?page=gestao_ofertas");            
            exit;
        } else {
            $erro = "Erro ao atualizar a oferta.";
        }
        $stmt->close();
    } else {
        $erro = "Erro ao preparar a consulta.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
</head>
<body>
    <div class="form-box-editar">
        <h1>Editar Oferta</h1>

        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group-editar">
                <label for="titulo"><i class="fas fa-briefcase"></i> Título</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($oferta['titulo']) ?>" required>
            </div>

            <div class="input-group-editar">
                <label for="descricao"><i class="fas fa-align-left"></i> Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($oferta['descricao']) ?></textarea>
            </div>

            <div class="input-group-editar">
                <label for="data_inicio"><i class="fas fa-calendar-alt"></i> Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($oferta['data_inicio']) ?>" required>
            </div>

            <div class="input-group-editar">
                <label for="data_fim"><i class="fas fa-calendar-check"></i> Data Fim</label>
                <input type="date" id="data_fim" name="data_fim" value="<?= htmlspecialchars($oferta['data_fim']) ?>" required>
            </div>

            <div class="input-group-editar">
                <label for="vagas"><i class="fas fa-users"></i> Número de Vagas</label>
                <input type="number" id="vagas" name="vagas" value="<?= htmlspecialchars($oferta['vagas']) ?>" required>
            </div>

            <button type="submit" class="btn-editar-submit">Guardar Alterações</button>
        </form>
    </div>
</body>
</html>