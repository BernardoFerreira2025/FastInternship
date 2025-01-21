<?php
    require_once '../database/mysqli.php';

    if ($conn) {
        echo "Conexão estabelecida com sucesso!<br>";

        $sql_ofertas_teste = "SELECT 1";
        $result_teste = $conn->query($sql_ofertas_teste);

        if($result_teste) {
             echo "Query executada com sucesso!<br>";
         } else {
                echo "Erro na query SQL: " . $conn->error;
        }
       } else {
            echo "Falha ao conectar com a base de dados: ";
            echo mysqli_connect_error();
        }
    // Obter ofertas disponíveis com informações adicionais
    $sql_ofertas = "SELECT o.*, e.nome_empresa as empresa_nome, e.responsavel as empresa_responsavel
                    FROM ofertas_empresas o 
                    INNER JOIN empresas e ON o.id_empresa = e.id_empresas";

    echo "SQL Query: " . $sql_ofertas . "<br>";
      if ($conn instanceof mysqli) {
            $result_ofertas = $conn->query($sql_ofertas);
        if ($result_ofertas) {
               $ofertas = $result_ofertas->fetch_all(MYSQLI_ASSOC);
                $conn->close();
           } else {
                 echo "Erro na query SQL: " . $conn->error;
                 echo mysqli_error($conn);
           }
       }
         else {
            echo "Erro: \$conn não é um objeto mysqli válido.<br>";
        }
?>
<h1>Ofertas Disponíveis</h1>
<?php if (isset($ofertas) && count($ofertas) > 0): ?>
    <ul>
        <?php foreach ($ofertas as $oferta): ?>
            <li>
                <h3><?php echo $oferta['titulo']; ?></h3>
                <p><strong>Empresa:</strong> <?php echo $oferta['empresa_nome']; ?></p>
                <p><strong>Responsável:</strong> <?php echo $oferta['empresa_responsavel']; ?></p>
                <p><strong>Descrição:</strong> <?php echo $oferta['descricao']; ?></p>
                <p><strong>Data de Início:</strong> <?php echo $oferta['data_inicio']; ?></p>
                <p><strong>Data de Fim:</strong> <?php echo $oferta['data_fim']; ?></p>
                <p><strong>Requisitos:</strong> <?php echo $oferta['requisitos']; ?></p>
                <p><strong>Vagas:</strong> <?php echo $oferta['vagas']; ?></p>
                <p><strong>Curso Relacionado:</strong> <?php echo $oferta['curso_relacionado']; ?></p>
                <a href="aluno_dashboard.php?page=candidatar&id=<?php echo $oferta['id_oferta']; ?>">Candidatar</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Não há ofertas disponíveis no momento.</p>
<?php endif; ?>