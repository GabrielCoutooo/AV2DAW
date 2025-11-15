<?php
include_once 'header.php';
require_once APP_PATH . '/config/auth-check.php';

if (!adminEstaLogado()) {
    header("Location: /AV2DAW/views/adm/login.html");
    exit;
}

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

<script src="../../public/js/adm_dashboard.js"></script>

</body>

</html>