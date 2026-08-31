<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\ProjetoModel;
use App\Models\RubricaModel;
use App\Models\BolsistaModel;
use App\Models\ProfessorModel;

final class DashboardTest extends CIUnitTestCase
{
    public function testDashboardCalculoMetricas(): void
    {
        $db = \Config\Database::connect();

        $totalProjetos = (new ProjetoModel())->countAllResults();
        $totalBolsistas = (new BolsistaModel())->countAllResults();
        $totalProfessores = (new ProfessorModel())->countAllResults();
        
        $saldoResult = $db->table('rubricas')->selectSum('saldo_disponivel', 'total')->get()->getRow();
        $saldoTotal = (float) ($saldoResult->total ?? 0.00);

        $this->assertIsInt($totalProjetos);
        $this->assertIsInt($totalBolsistas);
        $this->assertIsInt($totalProfessores);
        $this->assertIsFloat($saldoTotal);
    }
}

