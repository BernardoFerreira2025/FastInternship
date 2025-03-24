<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

// Verifica se o ID foi enviado
if (isset($_GET['id'])) {
    $id_oferta = intval($_GET['id']);

    // Busca os dados da oferta para edição
    $sql = "SELECT * FROM ofertas_empresas WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_oferta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $oferta = $resultado->fetch_assoc();
        $stmt->close();

        if (!$oferta) {
            $_SESSION['mensagem_erro'] = "Oferta não encontrada.";
            header("Location: gestao_ofertas.php");
            exit;
        }
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta.";
        header("Location: gestao_ofertas.php");
        exit;
    }
} else {
    $_SESSION['mensagem_erro'] = "ID da oferta não fornecido.";
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

    // Verificação no PHP: Impedir Data de Fim menor que Data de Início
    if (strtotime($data_fim) < strtotime($data_inicio)) {
        $_SESSION['mensagem_erro'] = "A Data de Fim não pode ser menor que a Data de Início.";
        header("Location: editar_oferta.php?id=$id_oferta");
        exit;
    }

    $sql = "UPDATE ofertas_empresas SET titulo = ?, descricao = ?, data_inicio = ?, data_fim = ?, vagas = ? WHERE id_oferta = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssii", $titulo, $descricao, $data_inicio, $data_fim, $vagas, $id_oferta);
        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Oferta alterada com sucesso!";
            header("Location: professor_dashboard.php?page=gestao_ofertas");
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao atualizar a oferta.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a consulta.";
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
    <div class="users-container">
        <h2 class="users-header">Editar Oferta</h2>
        <?php
        if (isset($_SESSION['mensagem_erro'])) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast('" . addslashes($_SESSION['mensagem_erro']) . "', 'error');
                    });
                  </script>";
            unset($_SESSION['mensagem_erro']);
        }
        ?>
        <form method="POST" action="" id="editarOfertaForm">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($oferta['titulo']) ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($oferta['descricao']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="data_inicio">Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($oferta['data_inicio']) ?>" required>
            </div>
            <div class="form-group">
                <label for="data_fim">Data Fim</label>
                <input type="date" id="data_fim" name="data_fim" value="<?= htmlspecialchars($oferta['data_fim']) ?>" required>
            </div>
            <div class="form-group">
                <label for="vagas">Número de Vagas</label>
                <input type="number" id="vagas" name="vagas" value="<?= htmlspecialchars($oferta['vagas']) ?>" required>
            </div>
            <button type="submit" class="btn-submit">Salvar Alterações</button>
        </form>
    </div>

    <script>
        document.getElementById('editarOfertaForm').addEventListener('submit', function(event) {
            const dataInicio = new Date(document.getElementById('data_inicio').value);
            const dataFim = new Date(document.getElementById('data_fim').value);

            if (dataFim < dataInicio) {
                showToast('A Data de Fim não pode ser menor que a Data de Início.', 'error');
                event.preventDefault();
            }
        });

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }
    </script>
</body>
</html>
