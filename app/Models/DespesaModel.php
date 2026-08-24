<?php

namespace App\Models;

use CodeIgniter\Model;

class DespesaModel extends Model
{
    protected $table            = 'despesas';
    protected $primaryKey       = 'id_despesa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Shadow Tables e Lixeira via Triggers SQLite

    protected $allowedFields    = [
        'id_projeto',
        'id_rubrica',
        'cnpj_fornecedor',
        'nome_fornecedor',
        'numero_nota',
        'data_emissao',
        'valor_total',
        'descricao_itens',
        'status_ocr',
        'status_aprovacao'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    // Callbacks de Auditoria
    protected $beforeInsert = ['setAuditoriaCriacao'];
    protected $beforeUpdate = ['setAuditoriaAtualizacao'];
    protected $beforeDelete = ['setAuditoriaExclusao'];

    protected function setAuditoriaCriacao(array $data)
    {
        if (isset($data['data'])) {
            $usuario = session()->get('login') ?? 'sistema';
            $data['data']['_criado_por']     = $usuario;
            $data['data']['_criado_em']      = date('Y-m-d H:i:s');
            $data['data']['_operacao']       = 'INSERT';
        }
        return $data;
    }

    protected function setAuditoriaAtualizacao(array $data)
    {
        if (isset($data['data'])) {
            $usuario = session()->get('login') ?? 'sistema';
            $data['data']['_atualizado_por'] = $usuario;
            $data['data']['_atualizado_em']  = date('Y-m-d H:i:s');
            $data['data']['_operacao']       = 'UPDATE';
        }
        return $data;
    }

    /**
     * Atualização relâmpago antes do DELETE físico para alimentar a pseudo-tabela OLD da trigger
     */
    protected function setAuditoriaExclusao(array $data)
    {
        if (!empty($data['id'])) {
            $usuario = session()->get('login') ?? 'sistema';
            $this->builder()
                 ->whereIn($this->primaryKey, (array) $data['id'])
                 ->update([
                     '_deletado_por'   => $usuario,
                     '_deletado_em'    => date('Y-m-d H:i:s'),
                     '_operacao'       => 'DELETE'
                 ]);
        }
        return $data;
    }

    /**
     * Consulta customizada trazendo os relacionamentos de Projeto e Rubrica
     */
    public function getDespesasComRelacionamentos(?int $idProjeto = null)
    {
        $builder = $this->db->table('despesas d')
            ->select('d.*, p.codigo_projeto_fundacao, p.titulo as projeto_titulo, r.nome as rubrica_nome, r.tipo as rubrica_tipo')
            ->join('projetos p', 'p.id_projeto = d.id_projeto', 'left')
            ->join('rubricas r', 'r.id_rubrica = d.id_rubrica', 'left')
            ->orderBy('d.data_emissao', 'DESC');

        if ($idProjeto) {
            $builder->where('d.id_projeto', $idProjeto);
        }

        return $builder->get()->getResultArray();
    }
}