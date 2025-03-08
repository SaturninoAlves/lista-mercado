// Dados armazenados em memória
let mercados = [];
let produtos = [];

// Elementos do DOM
const formMercado = document.getElementById('form-mercado');
const formProduto = document.getElementById('form-produto');
const selectMercado = document.getElementById('mercado');
const relatorio = document.getElementById('relatorio');

// Adicionar Mercado
formMercado.addEventListener('submit', function (e) {
    e.preventDefault();
    const nomeMercado = document.getElementById('nome-mercado').value;

    if (nomeMercado) {
        mercados.push({ id: mercados.length + 1, nome: nomeMercado });
        atualizarSelectMercados();
        formMercado.reset();
    }
});

// Adicionar Produto
formProduto.addEventListener('submit', function (e) {
    e.preventDefault();
    const mercadoId = parseInt(selectMercado.value);
    const nomeProduto = document.getElementById('nome-produto').value;
    const quantidade = parseFloat(document.getElementById('quantidade').value);
    const precoUnitario = parseFloat(document.getElementById('preco-unitario').value) || 0;
    const precoKg = parseFloat(document.getElementById('preco-kg').value) || 0;

    if (mercadoId && nomeProduto && quantidade) {
        const total = precoUnitario > 0 ? quantidade * precoUnitario : quantidade * precoKg;
        produtos.push({
            mercadoId,
            nome: nomeProduto,
            quantidade,
            precoUnitario,
            precoKg,
            total
        });
        formProduto.reset();
        gerarRelatorio();
    }
});

// Atualizar Select de Mercados
function atualizarSelectMercados() {
    selectMercado.innerHTML = mercados.map(mercado => `
        <option value="${mercado.id}">${mercado.nome}</option>
    `).join('');
}

// Gerar Relatório
function gerarRelatorio() {
    let html = '';
    mercados.forEach(mercado => {
        const produtosMercado = produtos.filter(p => p.mercadoId === mercado.id);
        if (produtosMercado.length > 0) {
            let totalMercado = 0;
            html += `<h3>${mercado.nome}</h3>`;
            html += `<table>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Preço por KG</th>
                            <th>Total</th>
                        </tr>`;
            produtosMercado.forEach(produto => {
                html += `<tr>
                            <td>${produto.nome}</td>
                            <td>${produto.quantidade}</td>
                            <td>R$ ${produto.precoUnitario.toFixed(2)}</td>
                            <td>R$ ${produto.precoKg.toFixed(2)}</td>
                            <td>R$ ${produto.total.toFixed(2)}</td>
                        </tr>`;
                totalMercado += produto.total;
            });
            html += `<tr class="total">
                        <td colspan="4">Total do Mercado</td>
                        <td>R$ ${totalMercado.toFixed(2)}</td>
                    </tr>`;
            html += `</table>`;
        }
    });
    relatorio.innerHTML = html;
}

// Inicializar
atualizarSelectMercados();