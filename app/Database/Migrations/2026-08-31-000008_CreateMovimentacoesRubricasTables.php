<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMovimentacoesRubricasTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: movimentacoes_rubricas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS movimentacoes_rubricas (
                id_movimentacao_rubrica INTEGER PRIMARY KEY AUTOINCREMENT,
                id_rubrica INTEGER NOT NULL,
                id_despesa INTEGER,
                tipo TEXT NOT NULL CHECK (tipo IN ('DESPESA', 'ESTORNO', 'AJUSTE', 'TRANSFERENCIA')),
                valor REAL NOT NULL,
                saldo_anterior REAL NOT NULL,
                saldo_posterior REAL NOT NULL,
                descricao TEXT,
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_rubrica) REFERENCES rubricas(id_rubrica)
            )
        ");

        // 2. Tabela de Histórico: movimentacoes_rubricas_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS movimentacoes_rubricas_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_movimentacao_rubrica INTEGER NOT NULL,
                id_rubrica INTEGER,
                id_despesa INTEGER,
                tipo TEXT,
                valor REAL,
                saldo_anterior REAL,
                saldo_posterior REAL,
                descricao TEXT,
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
            CREATE TRIGGER IF NOT EXISTS trigger_movimentacoes_rubricas_before_update 
            BEFORE UPDATE ON movimentacoes_rubricas FOR EACH ROW BEGIN
                INSERT INTO movimentacoes_rubricas_historico (id_movimentacao_rubrica, id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_em, _criado_por, _atualizado_em, _atualizado_por,  _operacao)
                VALUES (OLD.id_movimentacao_rubrica, OLD.id_rubrica, OLD.id_despesa, OLD.tipo, OLD.valor, OLD.saldo_anterior, OLD.saldo_posterior, OLD.descricao, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_movimentacoes_rubricas_before_delete 
            BEFORE DELETE ON movimentacoes_rubricas FOR EACH ROW BEGIN
                INSERT INTO movimentacoes_rubricas_historico (id_movimentacao_rubrica, id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_movimentacao_rubrica, OLD.id_rubrica, OLD.id_despesa, OLD.tipo, OLD.valor, OLD.saldo_anterior, OLD.saldo_posterior, OLD.descricao, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por,  OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_movimentacoes_rubricas_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_movimentacoes_rubricas_before_delete");
        $this->db->query("DROP TABLE IF EXISTS movimentacoes_rubricas_historico");
        $this->db->query("DROP TABLE IF EXISTS movimentacoes_rubricas");
    }
}

