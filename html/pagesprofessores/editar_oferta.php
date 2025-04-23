<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

if (!isset($_GET['id'])) {
    $_SESSION['toast_message'] = "ID da oferta não fornecido.";
    header("Location: professor_dashboard.php?page=gestao_ofertas");
    exit();
}

$id_oferta = intval($_GET['id']);

// Processa POST (alterações)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $vagas = intval($_POST['vagas']);

    if (strtotime($data_fim) < strtotime($data_inicio)) {
        $_SESSION['toast_message'] = "Erro: A Data de Fim não pode ser menor que a Data de Início.";
        header("Location: professor_dashboard.php?page=editar_oferta&id=$id_oferta");
        exit();
    }

    $sql = "UPDATE ofertas_empresas SET titulo = ?, descricao = ?, data_inicio = ?, data_fim = ?, vagas = ? WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssii", $titulo, $descricao, $data_inicio, $data_fim, $vagas, $id_oferta);
        if ($stmt->execute()) {
            $_SESSION['toast_message'] = "Alterações guardadas com sucesso!";
        } else {
            $_SESSION['toast_message'] = "Erro ao atualizar a oferta.";
        }
        $stmt->close();
    } else {
        $_SESSION['toast_message'] = "Erro ao preparar a consulta.";
    }

    header("Location: professor_dashboard.php?page=gestao_ofertas");
    exit();
}

// Se for GET, busca os dados para o formulário
$sql = "SELECT * FROM ofertas_empresas WHERE id_oferta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_oferta);
$stmt->execute();
$resultado = $stmt->get_result();
$oferta = $resultado->fetch_assoc();
$stmt->close();

if (!$oferta) {
    $_SESSION['toast_message'] = "Erro: Oferta não encontrada.";
    header("Location: professor_dashboard.php?page=gestao_ofertas");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Editar Oferta</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
</head>
<body>
    <div class="form-box-editar">
        <h1>Editar Oferta</h1>
        <form method="POST">
            <div class="input-group-editar">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($oferta['titulo']) ?>" required>
            </div>

            <div class="input-group-editar">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($oferta['descricao']) ?></textarea>
            </div>

            <div class="input-group-editar">
                <label for="data_inicio">Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?= $oferta['data_inicio'] ?>" required>
            </div>

            <div class="input-group-editar">
                <label for="data_fim">Data Fim</label>
                <input type="date" id="data_fim" name="data_fim" value="<?= $oferta['data_fim'] ?>" required>
            </div>

            <div class="input-group-editar">
                <label for="vagas">Número de Vagas</label>
                <input type="number" id="vagas" name="vagas" value="<?= $oferta['vagas'] ?>" required>
            </div>

            <button type="submit" class="btn-editar-submit">Guardar Alterações</button>
        </form>
    </div>
</body>
</html>
