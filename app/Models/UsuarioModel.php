<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Desativado pois a retenção histórica é feita no SQLite via triggers
    protected $useSoftDeletes   = false;

    // Colunas autorizadas para operações de escrita (INSERT/UPDATE)
    protected $allowedFields    = [
        'nome',
        'email',
        'login',
        'senha',
        'perfil',
        'ativo',
        '_criado_por',
        '_criado_em',
        '_atualizado_por',
        '_atualizado_em',
        '_deletado_por',
        '_deletado_em',
        '_operacao'
    ];

    // Timestamps manuais via callbacks de auditoria
    protected $useTimestamps    = false;
    protected $dateFormat       = 'datetime';

    // Callbacks de Auditoria
    protected $beforeInsert     = ['setAuditoriaCriacao'];
    protected $beforeUpdate     = ['setAuditoriaAtualizacao'];
    protected $beforeDelete     = ['setAuditoriaExclusao'];

    /**
     * Define quem criou o registro baseado na sessão ativa
     */
    protected function setAuditoriaCriacao(array $data): array
    {
        if (isset($data['data'])) {
            $usuario = session()->get('login') ?? 'sistema';
            $data['data']['_criado_por']     = $usuario;
            $data['data']['_criado_em']      = date('Y-m-d H:i:s');
            $data['data']['_operacao']       = 'INSERT';
        }
        return $data;
    }

    /**
     * Define quem atualizou o registro baseado na sessão ativa
     */
    protected function setAuditoriaAtualizacao(array $data): array
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
     * Define quem excluiu o registro antes do DELETE físico para alimentar a pseudo-tabela OLD da trigger
     */
    protected function setAuditoriaExclusao(array $data): array
    {
        if (!empty($data['id'])) {
            $usuario = session()->get('login') ?? 'sistema';
            $this->builder()
                 ->whereIn($this->primaryKey, (array) $data['id'])
                 ->update([
                     '_atualizado_por' => $usuario,
                     '_deletado_por'   => $usuario,
                     '_deletado_em'    => date('Y-m-d H:i:s'),
                     '_operacao'       => 'DELETE'
                 ]);
        }
        return $data;
    }
}
