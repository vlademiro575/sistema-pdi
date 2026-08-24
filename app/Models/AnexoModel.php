<?php

namespace App\Models;

use CodeIgniter\Model;

class AnexoModel extends Model
{
    protected $table            = 'anexos';
    protected $primaryKey       = 'id_anexo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_despesa',
        'nome_arquivo',
        'tipo',
        'url'
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
            $usuario = session()->get('login') ?? 'sistema';
            $data['data']['_atualizado_por'] = $usuario;
            $data['data']['_atualizado_em']  = date('Y-m-d H:i:s');
            $data['data']['_operacao']       = 'UPDATE';
        }
        return $data;
    }

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
}