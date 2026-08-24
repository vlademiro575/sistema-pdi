<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjetoModel extends Model
{
    protected $table            = 'projetos';
    protected $primaryKey       = 'id_projeto';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Desativado pois a retenção histórica é feita no SQLite via triggers
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_professor', 'id_fundacao', 'codigo_projeto_fundacao', 
        'titulo', 'orcamento_total', 'data_inicio', 'data_fim'
    ];

 

    // Timestamps
    protected $useTimestamps   = false; // Evita preenchimento automático de timestamps
    protected $dateFormat      = 'datetime';

    // Callbacks de Auditoria
    protected $beforeInsert    = ['setAuditoriaCriacao'];
    protected $beforeUpdate    = ['setAuditoriaAtualizacao'];
    protected $beforeDelete    = ['setAuditoriaExclusao'];

    /**
     * Define quem criou o registro baseado na sessão ativa
     */
    protected function setAuditoriaCriacao(array $data)
    {
        if (isset($data['data'])) {
            $data['data']['_criado_por']    = session()->get('login') ?? 'sistema';
            $data['data']['_criado_em']     = date('Y-m-d H:i:s');
            $data['data']['_operacao']     = 'INSERT';
        }
        return $data;
    }

    /**
     * Define quem atualizou o registro baseado na sessão ativa
     */
    protected function setAuditoriaAtualizacao(array $data)
    {
        if (isset($data['data'])) {
            $data['data']['_atualizado_por'] = session()->get('login') ?? 'sistema';
            $data['data']['_atualizado_em']  = date('Y-m-d H:i:s');
            $data['data']['_operacao']     = 'UPDATE';
        }
        return $data;
    }

    /**
     * Define quem excluiu o registro baseado na sessão ativa
     */
    protected function setAuditoriaExclusao(array $data)
    {

        // ⚠️ ATENÇÃO: Usamos $this->builder() para fazer o update direto!
        // Não use $this->update() aqui, senão você criará um loop infinito 
        // chamando os eventos de beforeUpdate.
        $this->builder()
             ->whereIn($this->primaryKey, (array) $data['id'])
             ->update([
                 '_deletado_por' =>  session()->get('login')  ?? 'sistema',
                 '_deletado_em' => date('Y-m-d H:i:s') ,
                 '_operacao'     => 'DELETE'
             ]);

        return $data;
    }
}