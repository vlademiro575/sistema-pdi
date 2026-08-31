<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProfessoresTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: professores
        $this->db->query("
            CREATE TABLE IF NOT EXISTS professores (
                id_professor INTEGER PRIMARY KEY AUTOINCREMENT,
                id_usuario INTEGER UNIQUE,
                nome TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                telefone TEXT,
                cpf TEXT NOT NULL UNIQUE,
                siape TEXT,
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
            )
        ");

        // 2. Tabela de Histórico: professores_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS professores_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_professor INTEGER NOT NULL,
                id_usuario INTEGER,
                nome TEXT,
                email TEXT,
                telefone TEXT,
                cpf TEXT,
                siape TEXT,
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
            CREATE TRIGGER IF NOT EXISTS trigger_professores_before_update 
            BEFORE UPDATE ON professores FOR EACH ROW BEGIN
                INSERT INTO professores_historico (id_professor, id_usuario, nome, email, telefone, cpf, siape, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
                VALUES (OLD.id_professor, OLD.id_usuario, OLD.nome, OLD.email, OLD.telefone, OLD.cpf, OLD.siape, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_professores_before_delete 
            BEFORE DELETE ON professores FOR EACH ROW BEGIN
                INSERT INTO professores_historico (id_professor, id_usuario, nome, email, telefone, cpf, siape, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_professor, OLD.id_usuario, OLD.nome, OLD.email, OLD.telefone, OLD.cpf, OLD.siape, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, 'DELETE');
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_professores_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_professores_before_delete");
        $this->db->query("DROP TABLE IF EXISTS professores_historico");
        $this->db->query("DROP TABLE IF EXISTS professores");
    }
}

