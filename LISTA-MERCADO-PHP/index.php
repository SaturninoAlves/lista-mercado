<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Mercado</title>
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .form-section { margin-bottom: 40px; border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; }
        .btn { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .report { margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Formulário para Adicionar Mercado -->
        <div class="form-section">
            <h2>Adicionar Mercado</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nome do Mercado:</label>
                    <input type="text" name="nome_mercado" required>
                </div>
                <button type="submit" name="add_mercado" class="btn">Adicionar Mercado</button>
            </form>
        </div>

        <!-- Formulário para Adicionar Produto -->
        <div class="form-section">
            <h2>Adicionar Produto</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nome do Produto:</label>
                    <input type="text" name="nome_produto" required>
                </div>
                <div class="form-group">
                    <label>Preço Unitário:</label>
                    <input type="number" step="0.01" name="preco_unitario">
                </div>
                <div class="form-group">
                    <label>Preço por KG:</label>
                    <input type="number" step="0.01" name="preco_kg">
                </div>
                <button type="submit" name="add_produto" class="btn">Adicionar Produto</button>
            </form>
        </div>

        <!-- Formulário para Criar Lista -->
        <div class="form-section">
            <h2>Criar Lista de Compras</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Selecione o Mercado:</label>
                    <select name="mercado_id" required>
                        <?php
                        $result = $conn->query("SELECT * FROM mercados");
                        while($row = $result->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id'] ?>"><?= $row['nome'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Selecione o Produto:</label>
                    <select name="produto_id" required>
                        <?php
                        $result = $conn->query("SELECT * FROM produtos");
                        while($row = $result->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id'] ?>"><?= $row['nome'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantidade:</label>
                    <input type="number" step="0.01" name="quantidade" required>
                </div>
                <button type="submit" name="add_lista" class="btn">Adicionar à Lista</button>
            </form>
        </div>

        <!-- Relatórios -->
        <div class="report">
            <h2>Relatório de Listas</h2>
            <?php
            $mercados = $conn->query("SELECT * FROM mercados");
            while($mercado = $mercados->fetch_assoc()):
                $total_mercado = 0;
            ?>
                <h3><?= $mercado['nome'] ?></h3>
                <table>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Preço por KG</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $lista = $conn->query("
                        SELECT p.nome, l.quantidade, p.preco_unitario, p.preco_kg 
                        FROM listas l 
                        JOIN produtos p ON l.produto_id = p.id 
                        WHERE l.mercado_id = {$mercado['id']}
                    ");
                    
                    while($item = $lista->fetch_assoc()):
                        $total_item = 0;
                        if($item['preco_unitario'] > 0) {
                            $total_item = $item['quantidade'] * $item['preco_unitario'];
                        } else {
                            $total_item = $item['quantidade'] * $item['preco_kg'];
                        }
                        $total_mercado += $total_item;
                    ?>
                        <tr>
                            <td><?= $item['nome'] ?></td>
                            <td><?= $item['quantidade'] ?></td>
                            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($item['preco_kg'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($total_item, 2, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="total">
                        <td colspan="4">Total do Mercado</td>
                        <td>R$ <?= number_format($total_mercado, 2, ',', '.') ?></td>
                    </tr>
                </table>
            <?php endwhile; ?>
        </div>
    </div>

    <?php
    // Processar formulários
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Adicionar Mercado
        if(isset($_POST['add_mercado'])) {
            $nome = $conn->real_escape_string($_POST['nome_mercado']);
            $conn->query("INSERT INTO mercados (nome) VALUES ('$nome')");
            header("Location: index.php");
        }

        // Adicionar Produto
        if(isset($_POST['add_produto'])) {
            $nome = $conn->real_escape_string($_POST['nome_produto']);
            $preco_unitario = $_POST['preco_unitario'] ?: 0;
            $preco_kg = $_POST['preco_kg'] ?: 0;
            
            $conn->query("INSERT INTO produtos (nome, preco_unitario, preco_kg) 
                         VALUES ('$nome', $preco_unitario, $preco_kg)");
            header("Location: index.php");
        }

        // Adicionar à Lista
        if(isset($_POST['add_lista'])) {
            $mercado_id = $_POST['mercado_id'];
            $produto_id = $_POST['produto_id'];
            $quantidade = $_POST['quantidade'];
            
            $conn->query("INSERT INTO listas (mercado_id, produto_id, quantidade) 
                         VALUES ($mercado_id, $produto_id, $quantidade)");
            header("Location: index.php");
        }
    }
    ?>
</body>
</html>