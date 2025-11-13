<?php
include_once 'header.php';

// Simulação de dados do banco - depois substituir por consultas reais
$veiculos = [
    ['id' => 1, 'modelo' => 'Koenigsegg', 'marca' => 'Koenigsegg', 'imagem' => 'https://images.unsplash.com/photo-1628889045175-6e31ce1d7b35?w=300', 'categoria' => 'Esportivo', 'disponivel' => true],
    ['id' => 2, 'modelo' => 'Nissan GT-R', 'marca' => 'Nissan', 'imagem' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=300', 'categoria' => 'Esportivo', 'disponivel' => true],
    ['id' => 3, 'modelo' => 'Rolls-Royce', 'marca' => 'Rolls-Royce', 'imagem' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=300', 'categoria' => 'Luxo', 'disponivel' => false],
    ['id' => 4, 'modelo' => 'All New Rush', 'marca' => 'Toyota', 'imagem' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=300', 'categoria' => 'SUV', 'disponivel' => true],
];

$vendedores = [
    ['nome' => 'CAUÁ', 'contato' => '(21) 96976-5432', 'turno' => '08:00 - 15:00', 'ultimo_modelo' => 'Nissan GT-R'],
    ['nome' => 'SOUZA', 'contato' => '(21) 96543-2109', 'turno' => '15:00 - 22:00', 'ultimo_modelo' => 'All New Rush'],
    ['nome' => 'ISAAS', 'contato' => '(21) 97654-3210', 'turno' => '15:00 - 22:00', 'ultimo_modelo' => 'Rolls-Royce'],
];

$checklists = [
    ['doc_cliente' => '485.780.362-19', 'modelo' => 'KOENIGSEGG', 'data' => '10/11/2024', 'tipo' => 'PÓS'],
    ['doc_cliente' => '803.214.135-62', 'modelo' => 'KOENIGSEGG', 'data' => '10/09/2024', 'tipo' => 'PRÉ'],
    ['doc_cliente' => '276.489.135-62', 'modelo' => 'ALL NEW RUSH', 'data' => '10/05/2024', 'tipo' => 'PÓS'],
];

$estatisticas = [
    'carros_alugados' => 36,
    'carros_disponiveis' => 24,
    'carros_manutencao' => 5,
    'vendas_mes' => 42
];
?>

<main>
    <!-- Seção de Frota -->
    <section class="frota">
        <h2>FROTA DE VEÍCULOS</h2>
        <button class="btn btn-primary" onclick="abrirModalCadastro()">+ Adicionar Veículo</button>
        
        <div class="frota-grid">
            <?php foreach ($veiculos as $veiculo): ?>
                <div class="veiculo-card" onclick="verDetalhesVeiculo(<?= $veiculo['id'] ?>)">
                    <img src="<?= $veiculo['imagem'] ?>" alt="<?= $veiculo['modelo'] ?>" class="veiculo-img">
                    <h3><?= $veiculo['marca'] ?> <?= $veiculo['modelo'] ?></h3>
                    <p><?= $veiculo['categoria'] ?></p>
                    <span style="color: <?= $veiculo['disponivel'] ? '#27ae60' : '#e74c3c' ?>;">
                        <?= $veiculo['disponivel'] ? 'Disponível' : 'Indisponível' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Seção de Vendedores -->
    <section class="vendedores">
        <h2>GERENCIAR VENDEDORES</h2>
        <table>
            <thead>
                <tr>
                    <th>NOME</th>
                    <th>CONTATO</th>
                    <th>TURNO</th>
                    <th>ÚLT. MODELO ALUG.</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendedores as $vendedor): ?>
                    <tr>
                        <td><?= $vendedor['nome'] ?></td>
                        <td><?= $vendedor['contato'] ?></td>
                        <td><?= $vendedor['turno'] ?></td>
                        <td><?= $vendedor['ultimo_modelo'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Seção de Checklists -->
    <section class="check-lists">
        <h2>ÚLTIMOS CHECK-LISTS</h2>
        <table>
            <thead>
                <tr>
                    <th>DOC. CLIENTE</th>
                    <th>MODELO</th>
                    <th>DATA</th>
                    <th>TIPO</th>
                    <th>AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checklists as $checklist): ?>
                    <tr>
                        <td><?= $checklist['doc_cliente'] ?></td>
                        <td><?= $checklist['modelo'] ?></td>
                        <td><?= $checklist['data'] ?></td>
                        <td><?= $checklist['tipo'] ?></td>
                        <td>
                            <button class="btn btn-primary" onclick="verChecklist('<?= $checklist['doc_cliente'] ?>', '<?= $checklist['data'] ?>')">
                                Ver Checklist
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Seção de Relatórios -->
    <section class="relatorios">
        <h2>INFORMAÇÕES GERAIS</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>CARROS ALUGADOS</h3>
                <div class="stat-number"><?= $estatisticas['carros_alugados'] ?></div>
                <p>Atualmente</p>
            </div>
            <div class="stat-card">
                <h3>CARROS DISPONÍVEIS</h3>
                <div class="stat-number"><?= $estatisticas['carros_disponiveis'] ?></div>
                <p>Para locação</p>
            </div>
            <div class="stat-card">
                <h3>EM MANUTENÇÃO</h3>
                <div class="stat-number"><?= $estatisticas['carros_manutencao'] ?></div>
                <p>Veículos</p>
            </div>
            <div class="stat-card">
                <h3>VENDAS MÊS</h3>
                <div class="stat-number"><?= $estatisticas['vendas_mes'] ?></div>
                <p>Últimos 30 dias</p>
            </div>
        </div>
    </section>
</main>

<!-- Modal para cadastro de veículo -->
<div id="modalCadastro" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="background: white; margin: 2rem auto; padding: 2rem; border-radius: 8px; max-width: 500px;">
        <h3>Cadastrar Novo Veículo</h3>
        <form id="formVeiculo" class="add-veiculo-form">
            <div class="form-group">
                <label>Modelo:</label>
                <input type="text" name="modelo" required>
            </div>
            <div class="form-group">
                <label>Marca:</label>
                <input type="text" name="marca" required>
            </div>
            <div class="form-group">
                <label>Categoria:</label>
                <select name="categoria" required>
                    <option value="Esportivo">Esportivo</option>
                    <option value="SUV">SUV</option>
                    <option value="Luxo">Luxo</option>
                    <option value="Compacto">Compacto</option>
                </select>
            </div>
            <div class="form-group">
                <label>URL da Imagem:</label>
                <input type="url" name="imagem" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-success">Cadastrar</button>
                <button type="button" class="btn" onclick="fecharModalCadastro()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Funções JavaScript para interatividade
function verDetalhesVeiculo(id) {
    window.location.href = 'gerenciar_veiculo.php?id=' + id;
}

function verChecklist(docCliente, data) {
    window.location.href = 'checklist.php?doc=' + docCliente + '&data=' + data;
}

function abrirModalCadastro() {
    document.getElementById('modalCadastro').style.display = 'block';
}

function fecharModalCadastro() {
    document.getElementById('modalCadastro').style.display = 'none';
}

// Fechar modal ao clicar fora
document.getElementById('modalCadastro').addEventListener('click', function(e) {
    if (e.target.id === 'modalCadastro') {
        fecharModalCadastro();
    }
});

// Envio do formulário via AJAX
document.getElementById('formVeiculo').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('cadastrar_veiculo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Veículo cadastrado com sucesso!');
            fecharModalCadastro();
            location.reload();
        } else {
            alert('Erro ao cadastrar veículo: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao cadastrar veículo');
    });
});
</script>

</body>
</html>