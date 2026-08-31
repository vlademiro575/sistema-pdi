<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjetosTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: projetos
        $this->db->query("
            CREATE TABLE IF NOT EXISTS projetos (
                id_projeto INTEGER PRIMARY KEY AUTOINCREMENT,
                id_professor INTEGER NOT NULL,
                id_fundacao INTEGER NOT NULL,
                codigo_projeto_fundacao TEXT NOT NULL,
                titulo TEXT NOT NULL,
                orcamento_total REAL NOT NULL,
                data_inicio DATE NOT NULL,
                data_fim DATE NOT NULL,
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_professor) REFERENCES professores(id_professor),
                FOREIGN KEY (id_fundacao) REFERENCES fundacoes(id_fundacao)
            )
        ");

        // 2. Tabela de Histórico: projetos_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS projetos_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_projeto INTEGER NOT NULL,
                id_professor INTEGER,
                id_fundacao INTEGER,
                codigo_projeto_fundacao TEXT,
                titulo TEXT,
                orcamento_total REAL,
                data_inicio DATE,
                data_fim DATE,
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
            CREATE TRIGGER IF NOT EXISTS trigger_projetos_before_update 
            BEFORE UPDATE ON projetos FOR EACH ROW BEGIN
                INSERT INTO projetos_historico (id_projeto, id_professor, id_fundacao, codigo_projeto_fundacao, titulo, orcamento_total, data_inicio, data_fim, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_projeto, OLD.id_professor, OLD.id_fundacao, OLD.codigo_projeto_fundacao, OLD.titulo, OLD.orcamento_total, OLD.data_inicio, OLD.data_fim, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_projetos_before_delete 
            BEFORE DELETE ON projetos FOR EACH ROW BEGIN
                INSERT INTO projetos_historico (id_projeto, id_professor, id_fundacao, codigo_projeto_fundacao, titulo, orcamento_total, data_inicio, data_fim, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao )
                VALUES (OLD.id_projeto, OLD.id_professor, OLD.id_fundacao, OLD.codigo_projeto_fundacao, OLD.titulo, OLD.orcamento_total, OLD.data_inicio, OLD.data_fim, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_projetos_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_projetos_before_delete");
        $this->db->query("DROP TABLE IF EXISTS projetos_historico");
        $this->db->query("DROP TABLE IF EXISTS projetos");
    }
}

