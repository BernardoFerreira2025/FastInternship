<?php
  echo dirname(__FILE__) . "<br>";
  require_once '../database/mysqli.php';

  // Obter o id da oferta da URL
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo "Erro: ID da oferta inválido";
        exit();
    }

    $id_oferta = $_GET['id'];
    $id_aluno = $_SESSION['id_aluno'];

    // Verificar se o aluno já se candidatou a esta oferta
    $sql_check = "SELECT id_candidatura FROM candidaturas WHERE id_aluno = ? AND id_oferta = ?";

    if($stmt = $conn->prepare($sql_check)) {

    $stmt->bind_param("ii", $id_aluno, $id_oferta);
    $stmt->execute();
    $stmt->store_result();

        if ($stmt->num_rows > 0) {
        $stmt->close();
           echo "Já se candidatou a esta oferta.";
        exit();
    }

    $stmt->close();
    }

 if($_SERVER["REQUEST_METHOD"] == "POST") {

     // Inserir nova candidatura
        $sql_insert = "INSERT INTO candidaturas (id_aluno, id_oferta, status, data_candidatura, carta_motivacao) VALUES (?, ?, 'pendente', CURRENT_TIMESTAMP, ?)";

           if($stmt = $conn->prepare($sql_insert)) {
               $motivacao = isset($_POST['motivacao']) ? $_POST['motivacao'] : '';

               $stmt->bind_param("iis", $id_aluno, $id_oferta, $motivacao);

                if ($stmt->execute()) {
                    $stmt->close();
                     header("Location: aluno_dashboard.php?page=verofertas&candidatura=sucesso");
                     exit();
                 } else {
                       echo "Erro ao inserir candidatura: " . $stmt->error;
                   }

           $stmt->close();
            }  else {
                echo "Erro ao preparar a query: " . $conn->error;
                }
          $conn->close();
      }
      else {
          ?>
          <h1>Candidatar a Oferta</h1>
         <form method="post">
             <label for="motivacao">Carta de Motivação</label><br>
             <textarea id="motivacao" name="motivacao" rows="4" cols="50"></textarea><br>
             <input type="submit" value="Candidatar">
        </form>
       <?php
      }
?>