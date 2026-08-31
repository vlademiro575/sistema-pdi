<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBolsistasTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: bolsistas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS bolsistas (
                id_bolsista INTEGER PRIMARY KEY AUTOINCREMENT,
                id_usuario INTEGER UNIQUE,
                nome TEXT NOT NULL,
                cpf TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                telefone TEXT,
                banco TEXT,
                agencia TEXT,
                conta_corrente TEXT,
                _criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
            )
        ");

        // 2. Tabela de Histórico: bolsistas_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS bolsistas_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_bolsista INTEGER NOT NULL,
                id_usuario INTEGER,
                nome TEXT,
                cpf TEXT,
                email TEXT,
                telefone TEXT,
                banco TEXT,
                agencia TEXT,
                conta_corrente TEXT,
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
            CREATE TRIGGER IF NOT EXISTS trigger_bolsistas_before_update 
            BEFORE UPDATE ON bolsistas FOR EACH ROW BEGIN
                INSERT INTO bolsistas_historico (id_bolsista, id_usuario, nome, cpf, email, telefone, banco, agencia, conta_corrente, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao )
                VALUES (OLD.id_bolsista, OLD.id_usuario, OLD.nome, OLD.cpf, OLD.email, OLD.telefone, OLD.banco, OLD.agencia, OLD.conta_corrente, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por , OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_bolsistas_before_delete 
            BEFORE DELETE ON bolsistas FOR EACH ROW BEGIN
                INSERT INTO bolsistas_historico (id_bolsista, id_usuario, nome, cpf, email, telefone, banco, agencia, conta_corrente, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_bolsista, OLD.id_usuario, OLD.nome, OLD.cpf, OLD.email, OLD.telefone, OLD.banco, OLD.agencia, OLD.conta_corrente, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_bolsistas_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_bolsistas_before_delete");
        $this->db->query("DROP TABLE IF EXISTS bolsistas_historico");
        $this->db->query("DROP TABLE IF EXISTS bolsistas");
    }
}

