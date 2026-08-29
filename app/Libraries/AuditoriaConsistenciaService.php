<?php

namespace App\Libraries;

use Config\Database;

class AuditoriaConsistenciaService
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Executa a auditoria completa de consistência e integridade das regras de negócio
     *
     * @return array
     */
    public function executarAuditoria(): array
    {
        $pendencias = [];
        $projetosComInconsistencia = [];

        // 1. Carrega todos os projetos
        $projetos = $this->db->table('projetos p')
            ->select('p.*, f.sigla as fundacao_sigla, f.nome as fundacao_nome, prof.nome as professor_nome')
            ->join('fundacoes f', 'f.id_fundacao = p.id_fundacao', 'left')
            ->join('professores prof', 'prof.id_professor = p.id_professor', 'left')
            ->orderBy('p.codigo_projeto_fundacao', 'ASC')
            ->get()
            ->getResultArray();

        $totalProjetos = count($projetos);

        // -------------------------------------------------------------
        // REGRA 1 & 2: Orçamento Total x Soma das Rubricas e Saldos
        // REGRA 1: Integridade de Vínculos (Fundação Inexistente)
        // REGRA 2 & 3: Orçamento Total x Soma das Rubricas e Saldos
        // -------------------------------------------------------------
        foreach ($projetos as $proj) {
            $idProjeto = (int) $proj['id_projeto'];
            $codProj   = $proj['codigo_projeto_fundacao'];
            $orcamento = (float) $proj['orcamento_total'];

            // Checagem de Fundação Inexistente
            if (empty($proj['fundacao_nome'])) {
                $idFundacao = $proj['id_fundacao'] ?? 'não informado';
                $pendencias[] = [
                    'tipo'        => 'ERRO',
                    'categoria'   => 'Vínculos Institucionais',
                    'id_projeto'  => $idProjeto,
                    'codigo'      => $codProj,
                    'titulo_proj' => $proj['titulo'],
                    'regra'       => 'Fundação Inexistente ou Inválida',
                    'mensagem'    => "O projeto está vinculado à fundação com ID #{$idFundacao}, mas esse registro não existe na tabela de fundações.",
                    'acao_url'    => base_url("projetos/editar/{$idProjeto}"),
                    'acao_texto'  => 'Vincular Fundação'
                ];
                $projetosComInconsistencia[$idProjeto] = true;
            }

            $rubricas = $this->db->table('rubricas')
                ->where('id_projeto', $idProjeto)
                ->get()
                ->getResultArray();

            if (empty($rubricas)) {
                if ($orcamento > 0) {
                    $pendencias[] = [
                        'tipo'        => 'AVISO',
                        'categoria'   => 'Orçamento & Rubricas',
                        'id_projeto'  => $idProjeto,
                        'codigo'      => $codProj,
                        'titulo_proj' => $proj['titulo'],
                        'regra'       => 'Projeto sem Rubricas Alocadas',
                        'mensagem'    => "O projeto possui orçamento aprovado de R$ " . number_format($orcamento, 2, ',', '.') . ", porém não possui nenhuma rubrica orçamentária cadastrada.",
                        'acao_url'    => base_url("projetos/gerenciar/{$idProjeto}#rubricas"),
                        'acao_texto'  => 'Adicionar Rubricas'
                    ];
                    $projetosComInconsistencia[$idProjeto] = true;
                }
            } else {
                $somaAlocado = 0.0;
                foreach ($rubricas as $rub) {
                    $valAlocado = (float) $rub['valor_alocado'];
                    $saldoDisp  = (float) $rub['saldo_disponivel'];
                    $somaAlocado += $valAlocado;

                    // Saldo negativo em rubrica
                    if ($saldoDisp < -0.009) {
                        $pendencias[] = [
                            'tipo'        => 'ERRO',
                            'categoria'   => 'Saldos & Rubricas',
                            'id_projeto'  => $idProjeto,
                            'codigo'      => $codProj,
                            'titulo_proj' => $proj['titulo'],
                            'regra'       => 'Saldo Negativo em Rubrica',
                            'mensagem'    => "A rubrica '{$rub['nome']}' ({$rub['tipo']}) está com saldo negativo de R$ " . number_format($saldoDisp, 2, ',', '.') . ".",
                            'acao_url'    => base_url("projetos/gerenciar/{$idProjeto}#rubricas"),
                            'acao_texto'  => 'Ajustar Saldo'
                        ];
                        $projetosComInconsistencia[$idProjeto] = true;
                    }

                    // Saldo disponível maior que o valor alocado
                    if ($saldoDisp > ($valAlocado + 0.01)) {
                        $pendencias[] = [
                            'tipo'        => 'AVISO',
                            'categoria'   => 'Saldos & Rubricas',
                            'id_projeto'  => $idProjeto,
                            'codigo'      => $codProj,
                            'titulo_proj' => $proj['titulo'],
                            'regra'       => 'Saldo Disponível Superior ao Alocado',
                            'mensagem'    => "A rubrica '{$rub['nome']}' possui saldo disponível (R$ " . number_format($saldoDisp, 2, ',', '.') . ") maior que o valor alocado inicial (R$ " . number_format($valAlocado, 2, ',', '.') . ").",
                            'acao_url'    => base_url("projetos/gerenciar/{$idProjeto}#rubricas"),
                            'acao_texto'  => 'Verificar Rubrica'
                        ];
                        $projetosComInconsistencia[$idProjeto] = true;
                    }
                }

                // Discrepância entre soma das rubricas e orçamento total do projeto
                $diferenca = round($orcamento - $somaAlocado, 2);
                if (abs($diferenca) >= 0.01) {
                    $tipoAlerta = 'ERRO';
                    if ($diferenca > 0) {
                        $msg = "A soma das rubricas (R$ " . number_format($somaAlocado, 2, ',', '.') . ") é menor que o orçamento total do projeto (R$ " . number_format($orcamento, 2, ',', '.') . "). Restam R$ " . number_format($diferenca, 2, ',', '.') . " sem distribuição orçamentária.";
                    } else {
                        $msg = "A soma das rubricas (R$ " . number_format($somaAlocado, 2, ',', '.') . ") ultrapassa o orçamento total do projeto (R$ " . number_format($orcamento, 2, ',', '.') . ") em R$ " . number_format(abs($diferenca), 2, ',', '.') . ".";
                    }

                    $pendencias[] = [
                        'tipo'        => $tipoAlerta,
                        'categoria'   => 'Orçamento & Rubricas',
                        'id_projeto'  => $idProjeto,
                        'codigo'      => $codProj,
                        'titulo_proj' => $proj['titulo'],
                        'regra'       => 'Soma de Rubricas Divergente do Orçamento',
                        'mensagem'    => $msg,
                        'acao_url'    => base_url("projetos/gerenciar/{$idProjeto}#rubricas"),
                        'acao_texto'  => 'Equalizar Rubricas'
                    ];
                    $projetosComInconsistencia[$idProjeto] = true;
                }
            }

            // Projetos com vigência vencida e saldo remanescente
            $hoje = date('Y-m-d');
            if ($proj['data_fim'] < $hoje) {
                $saldoRemanescente = $this->db->table('rubricas')
                    ->selectSum('saldo_disponivel', 'total_saldo')
                    ->where('id_projeto', $idProjeto)
                    ->get()
                    ->getRowArray()['total_saldo'] ?? 0;

                if ((float) $saldoRemanescente > 0.01) {
                    $pendencias[] = [
                        'tipo'        => 'INFO',
                        'categoria'   => 'Vigência & Prazos',
                        'id_projeto'  => $idProjeto,
                        'codigo'      => $codProj,
                        'titulo_proj' => $proj['titulo'],
                        'regra'       => 'Projeto Expirado com Saldo Remanescente',
                        'mensagem'    => "O projeto expirou em " . date('d/m/Y', strtotime($proj['data_fim'])) . " e ainda possui saldo disponível de R$ " . number_format($saldoRemanescente, 2, ',', '.') . " não executado.",
                        'acao_url'    => base_url("projetos/gerenciar/{$idProjeto}"),
                        'acao_texto'  => 'Ver Projeto'
                    ];
                }
            }
        }

        // -------------------------------------------------------------
        // REGRA 3: Despesas Fora da Vigência do Projeto
        // -------------------------------------------------------------
        $despesasVigencia = $this->db->table('despesas d')
            ->select('d.*, p.codigo_projeto_fundacao, p.titulo as projeto_titulo, p.data_inicio as proj_inicio, p.data_fim as proj_fim')
            ->join('projetos p', 'p.id_projeto = d.id_projeto')
            ->where('(d.data_emissao < p.data_inicio OR d.data_emissao > p.data_fim)')
            ->get()
            ->getResultArray();

        foreach ($despesasVigencia as $dv) {
            $idProjeto = (int) $dv['id_projeto'];
            $pendencias[] = [
                'tipo'        => 'ERRO',
                'categoria'   => 'Despesas & Prestação de Contas',
                'id_projeto'  => $idProjeto,
                'codigo'      => $dv['codigo_projeto_fundacao'],
                'titulo_proj' => $dv['projeto_titulo'],
                'regra'       => 'Despesa Fora da Vigência do Projeto',
                'mensagem'    => "A despesa Doc: '" . ($dv['numero_nota'] ?: 'S/N') . "' (Fornecedor: " . ($dv['nome_fornecedor'] ?: 'Não informado') . ") de R$ " . number_format($dv['valor_total'], 2, ',', '.') . " possui data de emissão (" . date('d/m/Y', strtotime($dv['data_emissao'])) . ") fora da vigência do projeto (" . date('d/m/Y', strtotime($dv['proj_inicio'])) . " a " . date('d/m/Y', strtotime($dv['proj_fim'])) . ").",
                'acao_url'    => base_url("despesas/editar/{$dv['id_despesa']}"),
                'acao_texto'  => 'Editar Despesa'
            ];
            $projetosComInconsistencia[$idProjeto] = true;
        }

        // -------------------------------------------------------------
        // REGRA 4: Despesas sem Comprovante / Anexo
        // -------------------------------------------------------------
        $despesasSemAnexo = $this->db->table('despesas d')
            ->select('d.*, p.codigo_projeto_fundacao, p.titulo as projeto_titulo')
            ->join('projetos p', 'p.id_projeto = d.id_projeto')
            ->join('anexos a', 'a.id_despesa = d.id_despesa', 'left')
            ->where('a.id_anexo IS NULL')
            ->get()
            ->getResultArray();

        foreach ($despesasSemAnexo as $dsa) {
            $idProjeto = (int) $dsa['id_projeto'];
            $pendencias[] = [
                'tipo'        => 'AVISO',
                'categoria'   => 'Despesas & Prestação de Contas',
                'id_projeto'  => $idProjeto,
                'codigo'      => $dsa['codigo_projeto_fundacao'],
                'titulo_proj' => $dsa['projeto_titulo'],
                'regra'       => 'Despesa sem Comprovante Anexo',
                'mensagem'    => "A despesa Doc: '" . ($dsa['numero_nota'] ?: 'S/N') . "' de R$ " . number_format($dsa['valor_total'], 2, ',', '.') . " não possui nenhum arquivo/nota fiscal em anexo.",
                'acao_url'    => base_url("despesas/editar/{$dsa['id_despesa']}"),
                'acao_texto'  => 'Anexar Comprovante'
            ];
            $projetosComInconsistencia[$idProjeto] = true;
        }

        // -------------------------------------------------------------
        // REGRA 5: Despesas em Análise (Aguardando Aprovação)
        // -------------------------------------------------------------
        $despesasEmAnalise = $this->db->table('despesas d')
            ->select('d.*, p.codigo_projeto_fundacao, p.titulo as projeto_titulo')
            ->join('projetos p', 'p.id_projeto = d.id_projeto')
            ->where('d.status_aprovacao', 'EM_ANALISE')
            ->get()
            ->getResultArray();

        foreach ($despesasEmAnalise as $dea) {
            $pendencias[] = [
                'tipo'        => 'INFO',
                'categoria'   => 'Despesas & Prestação de Contas',
                'id_projeto'  => (int) $dea['id_projeto'],
                'codigo'      => $dea['codigo_projeto_fundacao'],
                'titulo_proj' => $dea['projeto_titulo'],
                'regra'       => 'Despesa Aguardando Avaliação',
                'mensagem'    => "A despesa Doc: '" . ($dea['numero_nota'] ?: 'S/N') . "' de R$ " . number_format($dea['valor_total'], 2, ',', '.') . " está com status EM ANÁLISE e aguarda prestação de contas.",
                'acao_url'    => base_url("despesas"),
                'acao_texto'  => 'Avaliar no Painel'
            ];
        }

        // -------------------------------------------------------------
        // REGRA 6: Bolsistas com Período de Bolsa Fora da Vigência do Projeto
        // -------------------------------------------------------------
        $bolsistasDesalinhados = $this->db->table('projetos_bolsistas pb')
            ->select('pb.*, b.nome as bolsista_nome, p.codigo_projeto_fundacao, p.titulo as projeto_titulo, p.data_inicio as proj_inicio, p.data_fim as proj_fim')
            ->join('bolsistas b', 'b.id_bolsista = pb.id_bolsista')
            ->join('projetos p', 'p.id_projeto = pb.id_projeto')
            ->where('(pb.data_inicio < p.data_inicio OR (pb.data_fim IS NOT NULL AND pb.data_fim > p.data_fim))')
            ->get()
            ->getResultArray();

        foreach ($bolsistasDesalinhados as $bd) {
            $idProjeto = (int) $bd['id_projeto'];
            $dataFimBolsa = $bd['data_fim'] ? date('d/m/Y', strtotime($bd['data_fim'])) : 'Indefinido';
            $pendencias[] = [
                'tipo'        => 'AVISO',
                'categoria'   => 'Equipe & Bolsistas',
                'id_projeto'  => $idProjeto,
                'codigo'      => $bd['codigo_projeto_fundacao'],
                'titulo_proj' => $bd['projeto_titulo'],
                'regra'       => 'Bolsa Fora da Vigência do Projeto',
                'mensagem'    => "O bolsista '{$bd['bolsista_nome']}' possui vigência de bolsa (" . date('d/m/Y', strtotime($bd['data_inicio'])) . " a {$dataFimBolsa}) fora do período oficial do projeto (" . date('d/m/Y', strtotime($bd['proj_inicio'])) . " a " . date('d/m/Y', strtotime($bd['proj_fim'])) . ").",
                'acao_url'    => base_url("projetos/gerenciar/{$idProjeto}#bolsistas"),
                'acao_texto'  => 'Ajustar Bolsista'
            ];
            $projetosComInconsistencia[$idProjeto] = true;
        }

        // -------------------------------------------------------------
        // Consolidação dos Totais e Resumo
        // -------------------------------------------------------------
        $totalErros   = count(array_filter($pendencias, fn($p) => $p['tipo'] === 'ERRO'));
        $totalAvisos  = count(array_filter($pendencias, fn($p) => $p['tipo'] === 'AVISO'));
        $totalInfo    = count(array_filter($pendencias, fn($p) => $p['tipo'] === 'INFO'));
        $qtdProjetosComProblema = count($projetosComInconsistencia);
        $totalProjetosRegulares = max(0, $totalProjetos - $qtdProjetosComProblema);

        // Agrupamento de pendências por projeto para visualização organizada
        $pendenciasPorProjeto = [];
        foreach ($pendencias as $item) {
            $chave = $item['codigo'] . ' - ' . $item['titulo_proj'];
            $pendenciasPorProjeto[$chave][] = $item;
        }

        return [
            'resumo' => [
                'total_projetos'           => $totalProjetos,
                'total_projetos_regulares' => $totalProjetosRegulares,
                'total_erros'              => $totalErros,
                'total_avisos'             => $totalAvisos,
                'total_info'               => $totalInfo,
                'total_pendencias'         => count($pendencias),
                'percentual_conformidade'  => $totalProjetos > 0 ? round(($totalProjetosRegulares / $totalProjetos) * 100, 1) : 100
            ],
            'pendencias'             => $pendencias,
            'pendencias_por_projeto' => $pendenciasPorProjeto
        ];
    }
}

