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
                <option value="Esportivo">Esportivo</option>
                <option value="SUV">SUV</option>
                <option value="Luxo">Luxo</option>
                <option value="Econômico">Econômico</option>
                <option value="Sedan">Sedan</option>
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
                <option value="Esportivo">Esportivo</option>
                <option value="SUV">SUV</option>
                <option value="Luxo">Luxo</option>
                <option value="Econômico">Econômico</option>
                <option value="Sedan">Sedan</option>
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
<script src="../../public/js/adm_dashboard.js"></script>

</body>

</html>