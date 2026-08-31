<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogsOcrTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: logs_ocr
        $this->db->query("
            CREATE TABLE IF NOT EXISTS logs_ocr (
                id_log_ocr INTEGER PRIMARY KEY AUTOINCREMENT,
                id_despesa INTEGER NOT NULL,
                motor TEXT NOT NULL,
                texto_extraido TEXT,
                confianca REAL,
                tempo_execucao REAL,
                status TEXT DEFAULT 'SUCESSO' CHECK (status IN ('SUCESSO', 'ERRO', 'PARCIAL')),
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                FOREIGN KEY (id_despesa) REFERENCES despesas(id_despesa)
            )
        ");

        // 2. Tabela de Histórico: logs_ocr_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS logs_ocr_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_log_ocr INTEGER NOT NULL,
                id_despesa INTEGER,
                motor TEXT,
                texto_extraido TEXT,
                confianca REAL,
                tempo_execucao REAL,
                status TEXT,
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
            CREATE TRIGGER IF NOT EXISTS trigger_logs_ocr_before_update 
            BEFORE UPDATE ON logs_ocr FOR EACH ROW BEGIN
                INSERT INTO logs_ocr_historico (id_log_ocr, id_despesa, motor, texto_extraido, confianca, tempo_execucao, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_log_ocr, OLD.id_despesa, OLD.motor, OLD.texto_extraido, OLD.confianca, OLD.tempo_execucao, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_logs_ocr_before_delete 
            BEFORE DELETE ON logs_ocr FOR EACH ROW BEGIN
                INSERT INTO logs_ocr_historico (id_log_ocr, id_despesa, motor, texto_extraido, confianca, tempo_execucao, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por,  _deletado_em, _deletado_por, _operacao )
                VALUES (OLD.id_log_ocr, OLD.id_despesa, OLD.motor, OLD.texto_extraido, OLD.confianca, OLD.tempo_execucao, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por,  OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_logs_ocr_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_logs_ocr_before_delete");
        $this->db->query("DROP TABLE IF EXISTS logs_ocr_historico");
        $this->db->query("DROP TABLE IF EXISTS logs_ocr");
    }
}
