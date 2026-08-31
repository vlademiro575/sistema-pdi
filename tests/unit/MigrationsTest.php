<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

final class MigrationsTest extends CIUnitTestCase
{
    public function testTodasAsTabelasEHistoricosExistem(): void
    {
        $db = \Config\Database::connect();
        
        $expectedTables = [
            'usuarios', 'usuarios_historico',
            'bolsistas', 'bolsistas_historico',
            'professores', 'professores_historico',
            'fundacoes', 'fundacoes_historico',
            'projetos', 'projetos_historico',
            'rubricas', 'rubricas_historico',
            'projetos_bolsistas', 'projetos_bolsistas_historico',
            'movimentacoes_rubricas', 'movimentacoes_rubricas_historico',
            'despesas', 'despesas_historico',
            'anexos', 'anexos_historico',
            'logs_ocr', 'logs_ocr_historico',
            'historico_status_despesas', 'historico_status_despesas_historico'
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue($db->tableExists($table), "A tabela '{$table}' deveria existir no banco de dados.");
        }
    }

    public function testTriggersDeAuditoriaExistem(): void
    {
        $db = \Config\Database::connect();
        
        $expectedTriggers = [
            'trigger_usuarios_before_update',
            'trigger_usuarios_before_delete',
            'trigger_bolsistas_before_update',
            'trigger_bolsistas_before_delete',
            'trigger_professores_before_update',
            'trigger_professores_before_delete',
            'trigger_fundacoes_before_update',
            'trigger_fundacoes_before_delete',
            'trigger_projetos_before_update',
            'trigger_projetos_before_delete',
            'trigger_rubricas_before_update',
            'trigger_rubricas_before_delete',
            'trigger_projetos_bolsistas_before_update',
            'trigger_projetos_bolsistas_before_delete',
            'trigger_movimentacoes_rubricas_before_update',
            'trigger_movimentacoes_rubricas_before_delete',
            'trigger_despesa_after_insert',
            'trigger_despesa_before_update',
            'trigger_despesa_before_delete',
            'trigger_anexos_before_update',
            'trigger_anexos_before_delete',
            'trigger_logs_ocr_before_update',
            'trigger_logs_ocr_before_delete',
            'trigger_historico_status_despesas_before_update',
            'trigger_historico_status_despesas_before_delete'
        ];

        $triggersInDb = $db->query("SELECT name FROM sqlite_master WHERE type = 'trigger'")->getResultArray();
        $triggerNames = array_column($triggersInDb, 'name');

        foreach ($expectedTriggers as $trigger) {
            $this->assertContains($trigger, $triggerNames, "A trigger '{$trigger}' deveria existir no SQLite.");
        }
    }
}

