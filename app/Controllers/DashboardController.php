<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BolsistaModel;
use App\Models\ProfessorModel;
use App\Models\ProjetoModel;
use App\Models\RubricaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $projetoModel = new ProjetoModel();
        $rubricaModel = new RubricaModel();
        $bolsistaModel = new BolsistaModel();
        $professorModel = new ProfessorModel();

        // Total de projetos cadastrados no sistema
        $totalProjetos = $projetoModel->countAllResults();

        // Soma total do saldo disponível em todas as rubricas orçamentárias
        $db = \Config\Database::connect();
        $saldoResult = $db->table('rubricas')->selectSum('saldo_disponivel', 'total')->get()->getRow();
        $saldoTotal = (float) ($saldoResult->total ?? 0.00);

        $data = [
            'totalProjetos'    => $totalProjetos,
            'totalBolsistas'   => $bolsistaModel->countAllResults(),
            'totalProfessores' => $professorModel->countAllResults(),
            'saldoTotal'       => $saldoTotal,
        ];

        return view('dashboard/index', $data);
    }
}