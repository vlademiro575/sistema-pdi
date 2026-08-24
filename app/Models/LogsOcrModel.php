<?php
namespace App\Models;
use CodeIgniter\Model;

class LogsOcrModel extends Model
{
    protected $table            = 'logs_ocr';
    protected $primaryKey       = 'id_log_ocr';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_despesa', 'motor', 'texto_extraido', 'confianca', 'tempo_execucao', 'status'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    protected $beforeInsert = ['setAuditoriaCriacao'];
    protected $beforeUpdate = ['setAuditoriaAtualizacao'];
    protected $beforeDelete = ['setAuditoriaExclusao'];

    protected function setAuditoriaCriacao(array $data) {
        if (isset($data['data'])) {
            $usuario = session()->get('login') ?? 'sistema';
            $data['data']['_criado_por']     = $usuario;
            $data['data']['_criado_em']      = date('Y-m-d H:i:s');
        }
        return $data;
    }

    protected function setAuditoriaAtualizacao(array $data) {
        if (isset($data['data'])) {
            $data['data']['_atualizado_por'] = session()->get('login') ?? 'sistema';
            $data['data']['_atualizado_em']  = date('Y-m-d H:i:s');
        }
        return $data;
    }

    protected function setAuditoriaExclusao(array $data) {
        if (!empty($data['id'])) {
            $this->builder()->whereIn($this->primaryKey, (array) $data['id'])->update([
                '_deletado_por' => session()->get('login') ?? 'sistema',
                '_deletado_em'  => date('Y-m-d H:i:s')
            ]);
        }
        return $data;
    }
}