<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnexosTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: anexos
        $this->db->query("
            CREATE TABLE IF NOT EXISTS anexos (
                id_anexo INTEGER PRIMARY KEY AUTOINCREMENT,
                id_despesa INTEGER NOT NULL,
                nome_arquivo TEXT NOT NULL,
                tipo TEXT NOT NULL CHECK (tipo IN ('NOTA_FISCAL', 'BOLETO', 'COMPROVANTE_PIX', 'COMPROVANTE_TED', 'FOTO', 'PDF', 'OUTRO')),
                url TEXT NOT NULL,
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

        // 2. Tabela de Histórico: anexos_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS anexos_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_anexo INTEGER NOT NULL,
                id_despesa INTEGER,
                nome_arquivo TEXT,
                tipo TEXT,
                url TEXT,
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
            CREATE TRIGGER IF NOT EXISTS trigger_anexos_before_update 
            BEFORE UPDATE ON anexos FOR EACH ROW BEGIN
                INSERT INTO anexos_historico (id_anexo, id_despesa, nome_arquivo, tipo, url, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_anexo, OLD.id_despesa, OLD.nome_arquivo, OLD.tipo, OLD.url, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao);
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_anexos_before_delete 
            BEFORE DELETE ON anexos FOR EACH ROW BEGIN
                INSERT INTO anexos_historico (id_anexo, id_despesa, nome_arquivo, tipo, url, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_anexo, OLD.id_despesa, OLD.nome_arquivo, OLD.tipo, OLD.url, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao);
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_anexos_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_anexos_before_delete");
        $this->db->query("DROP TABLE IF EXISTS anexos_historico");
        $this->db->query("DROP TABLE IF EXISTS anexos");
    }
}

