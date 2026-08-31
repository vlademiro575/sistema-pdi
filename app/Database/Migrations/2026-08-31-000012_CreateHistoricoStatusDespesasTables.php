<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoricoStatusDespesasTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: historico_status_despesas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS historico_status_despesas (
                id_historico_status INTEGER PRIMARY KEY AUTOINCREMENT,
                id_despesa INTEGER NOT NULL,
                status_anterior TEXT,
                status_novo TEXT NOT NULL,
                justificativa TEXT,
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_despesa) REFERENCES despesas(id_despesa)
            )
        ");

        // 2. Tabela de Histórico: historico_status_despesas_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS historico_status_despesas_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_historico_status INTEGER NOT NULL,
                id_despesa INTEGER,
                status_anterior TEXT,
                status_novo TEXT,
                justificativa TEXT,
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT
            )
        ");

        // 3. Trigger BEFORE UPDATE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_historico_status_despesas_before_update 
            BEFORE UPDATE ON historico_status_despesas FOR EACH ROW BEGIN
                INSERT INTO historico_status_despesas_historico (id_historico_status, id_despesa, status_anterior, status_novo, justificativa, _criado_em, _criado_por, _atualizado_em, _atualizado_por,  _operacao)
                VALUES (OLD.id_historico_status, OLD.id_despesa, OLD.status_anterior, OLD.status_novo, OLD.justificativa, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao);
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_historico_status_despesas_before_delete 
            BEFORE DELETE ON historico_status_despesas FOR EACH ROW BEGIN
                INSERT INTO historico_status_despesas_historico (id_historico_status, id_despesa, status_anterior, status_novo, justificativa, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_historico_status, OLD.id_despesa, OLD.status_anterior, OLD.status_novo, OLD.justificativa, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, _deletado_em, _deletado_por, OLD._operacao);
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_historico_status_despesas_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_historico_status_despesas_before_delete");
        $this->db->query("DROP TABLE IF EXISTS historico_status_despesas_historico");
        $this->db->query("DROP TABLE IF EXISTS historico_status_despesas");
    }
}

