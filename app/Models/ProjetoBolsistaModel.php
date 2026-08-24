<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjetoBolsistaModel extends Model
{
    protected $table            = 'projetos_bolsistas';
    protected $primaryKey       = 'id_projeto_bolsista';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_projeto',
        'id_bolsista',
        'valor_bolsa',
        'data_inicio',
        'data_fim',
        'status'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    protected $beforeInsert = ['setAuditoriaCriacao'];
    protected $beforeUpdate = ['setAuditoriaAtualizacao'];
    protected $beforeDelete = ['setAuditoriaExclusao'];

    protected function setAuditoriaCriacao(array $data)
    {
        if (isset($data['data'])) {
            $data['data']['_criado_por'] = session()->get('login') ?? 'sistema';
            $data['data']['_criado_em']  = date('Y-m-d H:i:s');
            $data['data']['_operacao']   = 'INSERT';
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
        if (!empty($data['id'])) {
            $this->builder()
                 ->whereIn($this->primaryKey, (array) $data['id'])
                 ->update([
                     '_atualizado_por' => session()->get('login') ?? 'sistema',
                     '_deletado_por'   => session()->get('login') ?? 'sistema',
                     '_deletado_em'    => date('Y-m-d H:i:s'),
                     '_operacao'       => 'DELETE'
                 ]);
        }
        return $data;
    }
}