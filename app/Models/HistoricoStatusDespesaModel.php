<?php
namespace App\Models;
use CodeIgniter\Model;

class HistoricoStatusDespesaModel extends Model
{
    protected $table            = 'historico_status_despesas';
    protected $primaryKey       = 'id_historico_status';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Apenas os dados de negócio são permitidos. A auditoria é blindada.
    protected $allowedFields    = [
        'id_despesa',
        'status_anterior',
        'status_novo',
        'justificativa'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

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
            $data['data']['_atualizado_por'] = session()->get('login') ?? 'sistema';
            $data['data']['_atualizado_em']  = date('Y-m-d H:i:s');
            $data['data']['_operacao']       = 'UPDATE';
        }
        return $data;
    }

    protected function setAuditoriaExclusao(array $data)
    {
        // Sua correção aplicada: Mantendo apenas a semântica da deleção
        if (!empty($data['id'])) {
            $this->builder()
                 ->whereIn($this->primaryKey, (array) $data['id'])
                 ->update([
                     '_deletado_por' => session()->get('login') ?? 'sistema',
                     '_deletado_em'  => date('Y-m-d H:i:s'),
                     '_operacao'     => 'DELETE'
                 ]);
        }
        return $data;
    }
}