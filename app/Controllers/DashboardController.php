<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BolsistaModel;
use App\Models\ProfessorModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $bolsistaModel = new BolsistaModel();
        $professorModel = new ProfessorModel();

        $data = [
            'totalBolsistas'   => $bolsistaModel->countAllResults(), // Conta os bolsistas ativos no banco
            'totalProjetos'    => 0,
            'totalProfessores' => $professorModel->countAllResults(), // Conta os professores ativos no banco
            'saldoTotal'       => 0.00,
        ];

        return view('dashboard/index', $data);
        
    }
}