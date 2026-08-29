<?php
/*
Esta tela atua como o painel central do projeto, renderizando os dados mestres no topo, a estrutura de abas (tabs) e o Modal flutuante para a inclusão ágil das Rubricas.
*/
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('conteudo') ?>

    <!-- Breadcrumb e Ações Superiores -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('projetos') ?>">Projetos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($projeto['codigo_projeto_fundacao']) ?></li>
        </ol>
    </nav>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Painel do Projeto: <span class="text-primary"><?= esc($projeto['codigo_projeto_fundacao']) ?></span>
        </h1>
        <div>
            <a href="<?= base_url('projetos') ?>" class="btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Lista de Projetos
            </a>
            <a href="<?= base_url('projetos/editar/' . $projeto['id_projeto']) ?>" class="btn btn-sm btn-info shadow-sm">
                <i class="fas fa-pen fa-sm mr-1"></i> Editar Dados Mestre
            </a>
        </div>
    </div>

    <!-- Feedback de Erros e Validações -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Card com Resumo do Projeto Mestre -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= esc($projeto['titulo']) ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <strong class="text-gray-700">Orçamento Total Aprovado:</strong><br>
                    <span class="h5 text-success font-weight-bold">
                        R$ <?= number_format($projeto['orcamento_total'], 2, ',', '.') ?>
                    </span>
                </div>
                <div class="col-md-4 mb-2">
                    <strong class="text-gray-700">Vigência:</strong><br>
                    <span class="text-dark">
                        <?= date('d/m/Y', strtotime($projeto['data_inicio'])) ?> até <?= date('d/m/Y', strtotime($projeto['data_fim'])) ?>
                    </span>
                </div>
                <div class="col-md-4 mb-2">
                    <strong class="text-gray-700">Identificador Interno:</strong><br>
                    <span class="badge badge-primary">ID #<?= $projeto['id_projeto'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegação em Abas (Mestre-Detalhe) -->
    <ul class="nav nav-tabs font-weight-bold" id="projetoTab" role="tablist">
        <?php /* ABA DE RUBRICAS */ ?>
        <li class="nav-item">
            <a class="nav-link active" id="rubricas-tab" data-toggle="tab" href="#rubricas" role="tab" aria-controls="rubricas" aria-selected="true">
                <i class="fas fa-wallet mr-1"></i> Orçamento & Rubricas
            </a>
        </li>
        <?php /* ABA DE BOLSISTAS */ ?>
        <li class="nav-item">
            <a class="nav-link" id="bolsistas-tab" data-toggle="tab" href="#bolsistas" role="tab" aria-controls="bolsistas" aria-selected="false">
                <i class="fas fa-user-graduate mr-1"></i> Equipe (Bolsistas)
            </a>
        </li>
    </ul>

    <!-- Conteúdo das Abas -->
    <div class="tab-content bg-white shadow-sm border border-top-0 p-4 mb-4" id="projetoTabContent">
        
        <!-- ABA: RUBRICAS -->
        <div class="tab-pane fade show active" id="rubricas" role="tabpanel" aria-labelledby="rubricas-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0 font-weight-bold text-gray-800">Rubricas Alocadas</h5>
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalNovaRubrica">
                    <i class="fas fa-plus fa-sm mr-1"></i> Adicionar Rubrica
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nome da Rubrica</th>
                            <th>Tipo</th>
                            <th class="text-right">Valor Alocado</th>
                            <th class="text-right">Saldo Disponível</th>
                            <th class="text-center" style="width: 100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rubricas) && is_array($rubricas)): ?>
                            <?php foreach ($rubricas as $rub): ?>
                                <tr>
                                    <td class="font-weight-bold text-dark"><?= esc($rub['nome']) ?></td>
                                    <td>
                                        <?php 
                                            $badgeClass = match($rub['tipo']) {
                                                'CUSTEIO' => 'badge-info',
                                                'CAPITAL' => 'badge-warning',
                                                'BOLSAS'  => 'badge-success',
                                                default   => 'badge-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc($rub['tipo']) ?></span>
                                    </td>
                                    <td class="text-right">R$ <?= number_format($rub['valor_alocado'], 2, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold text-success">
                                        R$ <?= number_format($rub['saldo_disponivel'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center">

                                        <!-- Botão de Extrato (Leva para uma tela isolada de relatório) -->
                                        <a href="<?= base_url('movimentacoes/extrato/' . $rub['id_rubrica']) ?>" 
                                        class="btn btn-info btn-circle btn-sm shadow-sm" 
                                        title="Ver Extrato Financeiro">
                                            <i class="fas fa-list"></i>
                                        </a>

                                        <!-- Botão de Lançamento Manual (Ajuste) -->
                                        <button type="button" class="btn btn-warning btn-circle btn-sm shadow-sm btn-ajuste" 
                                                data-toggle="modal" data-target="#modalAjusteRubrica"
                                                data-idrubrica="<?= $rub['id_rubrica'] ?>"
                                                title="Ajuste Manual de Saldo">
                                            <i class="fas fa-coins"></i>
                                        </button>

                                        <!-- Botão de Excluir original -->
                                        <a href="<?= base_url('rubricas/delete/' . $rub['id_rubrica']) ?>" 
                                        class="btn btn-danger btn-circle btn-sm shadow-sm" 
                                        onclick="return confirm('Deseja realmente remover esta rubrica?');"
                                        title="Excluir Rubrica">
                                            <i class="fas fa-trash"></i>
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i> Nenhuma rubrica cadastrada neste projeto. Clique em <strong>Adicionar Rubrica</strong> para iniciar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- ABA BOLSISTAS -->
        <div class="tab-pane fade" id="bolsistas" role="tabpanel" aria-labelledby="bolsistas-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0 font-weight-bold text-gray-800">Equipe do Projeto</h5>
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalNovoBolsista">
                    <i class="fas fa-plus fa-sm mr-1"></i> Vincular Bolsista
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Bolsista</th>
                            <th>Vigência</th>
                            <th class="text-right">Valor da Bolsa</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($equipe) && is_array($equipe)): ?>
                            <?php foreach ($equipe as $membro): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-dark">
                                            <?= esc($membro['bolsista_nome'] ?? ('Cadastro #' . $membro['id_bolsista'])) ?>
                                        </div>
                                        <?php if (!empty($membro['bolsista_cpf'])): ?>
                                            <small class="text-muted"><i class="fas fa-id-card mr-1"></i>CPF: <?= esc($membro['bolsista_cpf']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($membro['bolsista_email'])): ?>
                                            <br><small class="text-muted"><i class="fas fa-envelope mr-1"></i><?= esc($membro['bolsista_email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($membro['data_inicio'])) ?> até 
                                        <?= $membro['data_fim'] ? date('d/m/Y', strtotime($membro['data_fim'])) : 'Indefinido' ?>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">
                                        R$ <?= number_format($membro['valor_bolsa'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $badgeStatus = match($membro['status']) {
                                                'ATIVO'    => 'badge-success',
                                                'INATIVO'  => 'badge-warning',
                                                'DESLIGADO'=> 'badge-danger',
                                                default    => 'badge-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeStatus ?>"><?= esc($membro['status']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-circle btn-sm shadow-sm btn-editar-bolsista" 
                                                data-toggle="modal" data-target="#modalEditarBolsista"
                                                data-idprojetobolsista="<?= $membro['id_projeto_bolsista'] ?>"
                                                data-nomebolsista="<?= esc($membro['bolsista_nome'] ?? ('Cadastro #' . $membro['id_bolsista'])) ?>"
                                                data-valorbolsa="<?= $membro['valor_bolsa'] ?>"
                                                data-datainicio="<?= $membro['data_inicio'] ?>"
                                                data-datafim="<?= $membro['data_fim'] ?>"
                                                data-status="<?= $membro['status'] ?>"
                                                title="Alterar Vínculo do Bolsista">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <a href="<?= base_url('projetos-bolsistas/delete/' . $membro['id_projeto_bolsista']) ?>" 
                                           class="btn btn-danger btn-circle btn-sm shadow-sm" 
                                           onclick="return confirm('Deseja desvincular este bolsista do projeto?');"
                                           title="Remover Vínculo">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mr-1"></i> Nenhum bolsista alocado neste projeto.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- MODAL: Inserir Rubrica -->
    <div class="modal fade" id="modalNovaRubrica" tabindex="-1" role="dialog" aria-labelledby="modalNovaRubricaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= base_url('rubricas/create') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <!-- Vínculo com a Chave Estrangeira do Projeto Mestre -->
                    <input type="hidden" name="id_projeto" value="<?= esc($projeto['id_projeto']) ?>">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="modalNovaRubricaLabel">
                            <i class="fas fa-wallet mr-1"></i> Nova Rubrica
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nome" class="font-weight-bold">Nome da Rubrica <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Material de Consumo, Diárias..." required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipo" class="font-weight-bold">Tipo da Rubrica <span class="text-danger">*</span></label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="" disabled selected>Selecione uma opção...</option>
                                <option value="CUSTEIO">Custeio</option>
                                <option value="CAPITAL">Capital</option>
                                <option value="BOLSAS">Bolsas</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="valor_alocado" class="font-weight-bold">Valor Alocado (R$) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="valor_alocado" id="valor_alocado" class="form-control" placeholder="0,00" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save mr-1"></i> Salvar Rubrica
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Vincular Bolsista -->
    <div class="modal fade" id="modalNovoBolsista" tabindex="-1" role="dialog" aria-labelledby="modalNovoBolsistaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="<?= base_url('projetos-bolsistas/create') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_projeto" value="<?= esc($projeto['id_projeto']) ?>">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="modalNovoBolsistaLabel">
                            <i class="fas fa-user-graduate mr-1"></i> Vincular Bolsista ao Projeto
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="id_bolsista" class="font-weight-bold">Selecionar Bolsista <span class="text-danger">*</span></label>
                            <select name="id_bolsista" id="id_bolsista" class="form-control" required>
                                <option value="" disabled selected>Escolha o bolsista...</option>
                                <?php foreach ($bolsistas_disponiveis as $bolsista): ?>
                                    <option value="<?= $bolsista['id_bolsista'] ?>">
                                        <?= esc($bolsista['nome']) ?> (CPF: <?= esc($bolsista['cpf']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="valor_bolsa" class="font-weight-bold">Valor Mensal (R$) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="valor_bolsa" id="valor_bolsa" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="data_inicio" class="font-weight-bold">Data de Início <span class="text-danger">*</span></label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="data_fim" class="font-weight-bold">Data Final Prevista</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="font-weight-bold">Status Inicial <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="ATIVO" selected>Ativo</option>
                                <option value="INATIVO">Inativo</option>
                                <option value="DESLIGADO">Desligado</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save mr-1"></i> Salvar Vínculo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Alterar Bolsista do Projeto -->
    <div class="modal fade" id="modalEditarBolsista" tabindex="-1" role="dialog" aria-labelledby="modalEditarBolsistaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formEditarBolsista" action="" method="post">
                    <?= csrf_field() ?>

                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title font-weight-bold" id="modalEditarBolsistaLabel">
                            <i class="fas fa-user-edit mr-1"></i> Alterar Vínculo do Bolsista
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Bolsista</label>
                            <input type="text" id="edit_nome_bolsista" class="form-control font-weight-bold bg-light" readonly disabled>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="edit_valor_bolsa" class="font-weight-bold">Valor Mensal (R$) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="valor_bolsa" id="edit_valor_bolsa" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="edit_data_inicio" class="font-weight-bold">Data de Início <span class="text-danger">*</span></label>
                                <input type="date" name="data_inicio" id="edit_data_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="edit_data_fim" class="font-weight-bold">Data Final Prevista</label>
                                <input type="date" name="data_fim" id="edit_data_fim" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit_status" class="font-weight-bold">Status do Vínculo <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="ATIVO">Ativo</option>
                                <option value="INATIVO">Inativo</option>
                                <option value="DESLIGADO">Desligado</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info px-4 font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Lançamento Manual -->
<div class="modal fade" id="modalAjusteRubrica" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('movimentacoes-rubricas/createAjuste') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- Chaves Estrangeiras Ocultas -->
                <input type="hidden" name="id_projeto" value="<?= esc($projeto['id_projeto']) ?>">
                <input type="hidden" name="id_rubrica" id="input_id_rubrica_ajuste" value="">
                
                <!-- Forçando o tipo para respeitar a constraint CHECK do banco -->
                <input type="hidden" name="tipo" value="AJUSTE">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-coins mr-1"></i> Lançamento Manual (Ajuste)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info shadow-sm small">
                        <i class="fas fa-info-circle"></i> Utilize valores <strong>positivos</strong> para adicionar saldo (Aporte/Crédito) e <strong>negativos</strong> para subtrair saldo (Débito/Tarifa).
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Valor do Ajuste (R$) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="valor" class="form-control" placeholder="Ex: 500.00 ou -150.00" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Descrição / Justificativa <span class="text-danger">*</span></label>
                        <input type="text" name="descricao" class="form-control" placeholder="Ex: Rendimentos bancários, correção..." required minlength="3">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 font-weight-bold text-dark">
                        <i class="fas fa-save mr-1"></i> Processar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?php /* Para que os Modais saibam os dados corretos ao serem abertos */ ?>
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Ativação da aba correta se houver hash na URL (ex: #bolsistas)
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }

        // Atualiza a URL hash ao trocar de aba
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;
        });

        // Intercepta o clique no botão de Ajuste para injetar o ID correto no formulário
        $('.btn-ajuste').on('click', function() {
            var idRubrica = $(this).data('idrubrica');
            $('#input_id_rubrica_ajuste').val(idRubrica);
        });

        // Intercepta o clique no botão de Editar Bolsista para preencher os campos do formulário
        $('.btn-editar-bolsista').on('click', function() {
            var idProjetoBolsista = $(this).data('idprojetobolsista');
            var nomeBolsista      = $(this).data('nomebolsista');
            var valorBolsa        = $(this).data('valorbolsa');
            var dataInicio        = $(this).data('datainicio');
            var dataFim           = $(this).data('datafim');
            var status            = $(this).data('status');

            $('#formEditarBolsista').attr('action', '<?= base_url('projetos-bolsistas/update') ?>/' + idProjetoBolsista);
            $('#edit_nome_bolsista').val(nomeBolsista);
            $('#edit_valor_bolsa').val(valorBolsa);
            $('#edit_data_inicio').val(dataInicio);
            $('#edit_data_fim').val(dataFim || '');
            $('#edit_status').val(status);
        });
    });
</script>
<?= $this->endSection() ?>