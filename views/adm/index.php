<?php
include_once 'header.php';
require_once __DIR__ . "/../../config/config.php";
require_once APP_PATH . "/config/auth-check.php";

/*if (!adminEstaLogado()) {
    header("Location: /AV2DAW/views/adm/login.html");
    exit;
}
    */

?>


<main>
    <section class="frota">
        <h2>FROTA DE VEÍCULOS</h2>
        <button class="btn btn-primary" onclick="abrirModalCadastro()">+ Adicionar Veículo</button>

        <div class="frota-grid" id="frota-grid">
            <p>Carregando veículos...</p>
        </div>
    </section>

    <section class="vendedores">
        <h2>GERENCIAR VENDEDORES</h2>
        <button class="btn btn-success" onclick="abrirModalCadastroVendedor()">+ Adicionar Vendedor</button>
        <table>
            <thead>
                <tr>
                    <th>NOME</th>
                    <th>CONTATO</th>
                    <th>TURNO</th>
                    <th>ÚLT. MODELO ALUG.</th>
                    <th>AÇÃO</th>
                </tr>
            </thead>
            <tbody id="vendedores-tabela">
            </tbody>
        </table>
    </section>

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
            <tbody id="checklists-tabela">
            </tbody>
        </table>
    </section>

    <section class="relatorios">
        <h2>INFORMAÇÕES GERAIS</h2>
        <div class="stats-grid" id="estatisticas-grid">
        </div>
    </section>
</main>
<!-- MODAL DE CADASTRO DE VEÍCULO -->
<div id="modalCadastro" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="fechar" onclick="fecharModalCadastro()">&times;</span>

        <h2>Cadastrar Veículo</h2>

        <form id="formCadastroVeiculo" enctype="multipart/form-data">

            <label>Modelo</label>
            <input type="text" name="modelo" required>

            <label>Marca</label>
            <input type="text" name="marca" required>

            <label>Ano</label>
            <input type="number" name="ano" min="1900" max="2099" required>

            <label>Categoria</label>
            <select name="categoria" required>
                <option value="">Selecione</option>
                <option value="Popular">Popular</option>
                <option value="Recomendado">Recomendado</option>
                <option value="Esportivo">Esportivo</option>
                <option value="SUV">SUV</option>
                <option value="Luxo">Luxo</option>
                <option value="Econômico">Econômico</option>
                <option value="Sedan">Sedan</option>
                <option value="Hatch Compacto">Hatch Compacto</option>
                <option value="SUV Compacto">SUV Compacto</option>
                <option value="Hatch Médio">Hatch Médio</option>
                <option value="Compacto">Compacto</option>
                <option value="Picape Compacta">Picape Compacta</option>
                <option value="Picape Leve">Picape Leve</option>
            </select>

            <label>Cor</label>
            <input type="text" name="cor" required>

            <label>Placa</label>
            <input type="text" name="placa" maxlength="7" required>

            <label>Preço da diária (R$)</label>
            <input type="number" name="preco_diaria_base" step="0.01" required>

            <label>Status</label>
            <select name="disponivel" id="disponivelInput" required>
                <option value="1">Disponível</option>
                <option value="0">indisponível</option>
            </select>

            <label>Imagem do veículo</label>
            <input type="file" name="imagem" accept="image/*">

            <button type="submit" class="btn btn-success">Salvar</button>
        </form>
    </div>
</div>

<!-- MODAL DE EDIÇÃO DE VEÍCULO -->
<div id="modalEdicao" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="fechar" onclick="fecharModalEdicao()">&times;</span>

        <h2>Editar Veículo</h2>

        <form id="formEdicaoVeiculo" enctype="multipart/form-data">
            <input type="hidden" name="id_veiculo">

            <label>Modelo</label>
            <input type="text" name="modelo" required>

            <label>Marca</label>
            <input type="text" name="marca" required>

            <label>Ano</label>
            <input type="number" name="ano" min="1900" max="2099" required>

            <label>Categoria</label>
            <select name="categoria" required>
                <option value="">Selecione</option>
                <option value="Popular">Popular</option>
                <option value="Recomendado">Recomendado</option>
                <option value="Esportivo">Esportivo</option>
                <option value="SUV">SUV</option>
                <option value="Luxo">Luxo</option>
                <option value="Econômico">Econômico</option>
                <option value="Sedan">Sedan</option>
                <option value="Hatch Compacta">Hatch Compacta</option>
                <option value="SUV Compacto">SUV Compacto</option>
                <option value="Hatch Médio">Hatch Médio</option>
                <option value="Compacto">Compacto</option>
                <option value="Picape Compacta">Picape Compacta</option>
                <option value="Picape Leve">Picape Leve</option>
            </select>

            <label>Cor</label>
            <input type="text" name="cor" required>

            <label>Placa</label>
            <input type="text" name="placa" maxlength="7" required>

            <label>Preço da diária (R$)</label>
            <input type="number" name="preco_diaria_base" step="0.01" required>

            <label>Status</label>
            <select name="disponivel" id="disponivelInputEdicao" required>
                <option value="1">Disponível</option>
                <option value="0">Indisponível</option>
            </select>

            <label>Imagem do veículo</label>
            <input type="file" name="imagem" accept="image/*">

            <button type="submit" class="btn btn-success">Atualizar</button>
            <button type="button" class="btn btn-danger" onclick="excluirVeiculo()">Excluir Veículo</button>
        </form>
    </div>
</div>

<!-- MODAL DE CADASTRO DE VENDEDOR -->
<div id="modalCadastroVendedor" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 650px;">
        <span class="fechar" onclick="fecharModalCadastroVendedor()">&times;</span>

        <h2 style="text-align: center;">CADASTRAR VENDEDOR</h2>

        <form id="formCadastroVendedor">
            <div class="form-group-inline">
                <label>Nome completo</label>
                <input type="text" name="nome" required>
                <label>CPF</label>
                <input type="text" name="cpf" required>
            </div>
            
            <div class="form-group-inline">
                <label>RG</label>
                <input type="text" name="rg">
                <label>Data de nascimento</label>
                <input type="date" name="data_nascimento">
                <label>Gênero</label>
                <select name="genero">
                    <option value="">Selecione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            
            <div class="form-group-inline">
                <label>Telefone</label>
                <input type="tel" name="telefone">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required>
                <label>Endereço completo</label>
                <input type="text" name="endereco">
            </div>

            <div class="form-group-inline">
                <label>Data de admissão</label>
                <input type="date" name="data_admissao">
                <label>Turno</label>
                <input type="text" name="turno">
                <label>Carteira de trabalho</label>
                <input type="text" name="carteira_trabalho">
            </div>

            <div class="form-group-inline">
                <label>Banco</label>
                <input type="text" name="banco">
                <label>Agência e conta</label>
                <input type="text" name="agencia_conta">
            </div>

            <div class="form-group-inline">
                <label>Senha de Acesso</label>
                <input type="password" name="senha" placeholder="Senha inicial do Admin" required>
                <label>Confirmar Senha</label>
                <input type="password" name="confirmar_senha" placeholder="Confirme a senha" required>
            </div>
            
            <p id="msg-cadastro-vendedor" style="color:red; margin-top:10px;"></p>

            <button type="submit" class="btn btn-salvar" style="margin-top: 15px;">FINALIZAR CADASTRO</button>
        </form>
    </div>
</div>

<style>
    .form-group-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .form-group-inline label,
    .form-group-inline input,
    .form-group-inline select {
        flex: 1 1 30%;
        min-width: 150px;
    }
    .form-group-inline input[type="text"],
    .form-group-inline input[type="email"],
    .form-group-inline input[type="tel"],
    .form-group-inline input[type="date"],
    .form-group-inline input[type="password"],
    .form-group-inline select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .btn-salvar {
        background-color: #3498db;
    }
</style>

<script>
    // Função global usada pelo JS do dashboard para abrir a página de Check-in
    function abrirCheckin(id_locacao) {
        if (!id_locacao) {
            alert('ID da locação inválido.');
            return;
        }
        // CORREÇÃO: usar caminho absoluto a partir da raiz do projeto para evitar 404
        window.location.href = `/AV2DAW/views/adm/checkin.php?id_locacao=${encodeURIComponent(id_locacao)}`;
    }
</script>

<script src="../../public/js/adm_dashboard.js"></script>

</body>

</html>