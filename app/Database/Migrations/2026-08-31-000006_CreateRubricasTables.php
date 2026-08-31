<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRubricasTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: rubricas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS rubricas (
                id_rubrica INTEGER PRIMARY KEY AUTOINCREMENT,
                id_projeto INTEGER NOT NULL,
                nome TEXT NOT NULL,
                tipo TEXT NOT NULL CHECK (tipo IN ('CUSTEIO', 'CAPITAL', 'BOLSAS')),
                valor_alocado REAL NOT NULL,
                saldo_disponivel REAL NOT NULL,
                _criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                _criado_por TEXT,
                _atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_projeto) REFERENCES projetos(id_projeto)
            )
        ");

        // 2. Tabela de Histórico: rubricas_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS rubricas_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_rubrica INTEGER NOT NULL,
                id_projeto INTEGER,
                nome TEXT,
                tipo TEXT,
                valor_alocado REAL,
                saldo_disponivel REAL,
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
            CREATE TRIGGER IF NOT EXISTS trigger_rubricas_before_update 
            BEFORE UPDATE ON rubricas FOR EACH ROW BEGIN
                INSERT INTO rubricas_historico (id_rubrica, id_projeto, nome, tipo, valor_alocado, saldo_disponivel, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_rubrica, OLD.id_projeto, OLD.nome, OLD.tipo, OLD.valor_alocado, OLD.saldo_disponivel, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_rubricas_before_delete 
            BEFORE DELETE ON rubricas FOR EACH ROW BEGIN
                INSERT INTO rubricas_historico (id_rubrica, id_projeto, nome, tipo, valor_alocado, saldo_disponivel, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_rubrica, OLD.id_projeto, OLD.nome, OLD.tipo, OLD.valor_alocado, OLD.saldo_disponivel, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_rubricas_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_rubricas_before_delete");
        $this->db->query("DROP TABLE IF EXISTS rubricas_historico");
        $this->db->query("DROP TABLE IF EXISTS rubricas");
    }
}

