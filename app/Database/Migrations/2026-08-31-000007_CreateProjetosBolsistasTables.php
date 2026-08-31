<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjetosBolsistasTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: projetos_bolsistas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS projetos_bolsistas (
                id_projeto_bolsista INTEGER PRIMARY KEY AUTOINCREMENT,
                id_projeto INTEGER NOT NULL,
                id_bolsista INTEGER NOT NULL,
                valor_bolsa REAL NOT NULL,
                data_inicio DATE NOT NULL,
                data_fim DATE,
                status TEXT DEFAULT 'ATIVO' CHECK (status IN ('ATIVO', 'INATIVO', 'DESLIGADO')),
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_projeto) REFERENCES projetos(id_projeto),
                FOREIGN KEY (id_bolsista) REFERENCES bolsistas(id_bolsista)
            )
        ");

        // 2. Tabela de Histórico: projetos_bolsistas_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS projetos_bolsistas_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_projeto_bolsista INTEGER NOT NULL,
                id_projeto INTEGER,
                id_bolsista INTEGER,
                valor_bolsa REAL,
                data_inicio DATE,
                data_fim DATE,
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
            CREATE TRIGGER IF NOT EXISTS trigger_projetos_bolsistas_before_update 
            BEFORE UPDATE ON projetos_bolsistas FOR EACH ROW BEGIN
                INSERT INTO projetos_bolsistas_historico (id_projeto_bolsista, id_projeto, id_bolsista, valor_bolsa, data_inicio, data_fim, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_projeto_bolsista, OLD.id_projeto, OLD.id_bolsista, OLD.valor_bolsa, OLD.data_inicio, OLD.data_fim, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_projetos_bolsistas_before_delete 
            BEFORE DELETE ON projetos_bolsistas FOR EACH ROW BEGIN
                INSERT INTO projetos_bolsistas_historico (id_projeto_bolsista, id_projeto, id_bolsista, valor_bolsa, data_inicio, data_fim, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_projeto_bolsista, OLD.id_projeto, OLD.id_bolsista, OLD.valor_bolsa, OLD.data_inicio, OLD.data_fim, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_projetos_bolsistas_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_projetos_bolsistas_before_delete");
        $this->db->query("DROP TABLE IF EXISTS projetos_bolsistas_historico");
        $this->db->query("DROP TABLE IF EXISTS projetos_bolsistas");
    }
}

