<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFundacoesTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: fundacoes
        $this->db->query("
            CREATE TABLE IF NOT EXISTS fundacoes (
                id_fundacao INTEGER PRIMARY KEY AUTOINCREMENT,
                sigla TEXT NOT NULL UNIQUE,
                nome TEXT NOT NULL,
                cnpj TEXT UNIQUE,
                tipo TEXT NOT NULL CHECK (tipo IN ('FUNDACAO_APOIO', 'FAP_ESTADUAL', 'ORGAO_FEDERAL')),
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT 
            )
        ");

        // 2. Tabela de Histórico: fundacoes_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS fundacoes_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_fundacao INTEGER NOT NULL,
                sigla TEXT,
                nome TEXT,
                cnpj TEXT,
                tipo TEXT,
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
            CREATE TRIGGER IF NOT EXISTS trigger_fundacoes_before_update 
            BEFORE UPDATE ON fundacoes FOR EACH ROW BEGIN
                INSERT INTO fundacoes_historico (id_fundacao, sigla, nome, cnpj, tipo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_fundacao, OLD.sigla, OLD.nome, OLD.cnpj, OLD.tipo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_fundacoes_before_delete 
            BEFORE DELETE ON fundacoes FOR EACH ROW BEGIN
                INSERT INTO fundacoes_historico (id_fundacao, sigla, nome, cnpj, tipo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_fundacao, OLD.sigla, OLD.nome, OLD.cnpj, OLD.tipo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao);
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_fundacoes_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_fundacoes_before_delete");
        $this->db->query("DROP TABLE IF EXISTS fundacoes_historico");
        $this->db->query("DROP TABLE IF EXISTS fundacoes");
    }
}

