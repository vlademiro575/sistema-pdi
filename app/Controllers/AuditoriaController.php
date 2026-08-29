<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AuditoriaConsistenciaService;

class AuditoriaController extends BaseController
{
    /**
     * Tela Principal da Verificação de Pendências (Auditoria de Consistência)
     */
    public function index()
    {
        $service = new AuditoriaConsistenciaService();
        $resultado = $service->executarAuditoria();

        $data = [
            'titulo'                 => 'Auditoria e Verificação de Pendências',
            'resumo'                 => $resultado['resumo'],
            'pendencias'             => $resultado['pendencias'],
            'pendencias_por_projeto' => $resultado['pendencias_por_projeto']
        ];

        return view('auditoria/index', $data);
    }
}

