<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuariosTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: usuarios
        $this->db->query("
            CREATE TABLE IF NOT EXISTS usuarios (
                id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                login TEXT NOT NULL UNIQUE,
                senha TEXT NOT NULL,
                perfil TEXT NOT NULL CHECK (perfil IN ('PROFESSOR', 'BOLSISTA', 'SECRETARIO', 'ADMIN')),
                ativo INTEGER DEFAULT 1 CHECK (ativo IN (0, 1)),
                _criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT
            )
        ");

        // 2. Tabela de Histórico: usuarios_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS usuarios_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_usuario INTEGER NOT NULL,
                nome TEXT,
                email TEXT,
                login TEXT,
                senha TEXT,
                perfil TEXT,
                ativo INTEGER,
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
            CREATE TRIGGER IF NOT EXISTS trigger_usuarios_before_update 
            BEFORE UPDATE ON usuarios FOR EACH ROW BEGIN
                INSERT INTO usuarios_historico (id_usuario, nome, email, login, senha, perfil, ativo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_usuario, OLD.nome, OLD.email, OLD.login, OLD.senha, OLD.perfil, OLD.ativo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");

        // 4. Trigger BEFORE DELETE
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_usuarios_before_delete 
            BEFORE DELETE ON usuarios FOR EACH ROW BEGIN
                INSERT INTO usuarios_historico (id_usuario, nome, email, login, senha, perfil, ativo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
                VALUES (OLD.id_usuario, OLD.nome, OLD.email, OLD.login, OLD.senha, OLD.perfil, OLD.ativo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_usuarios_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_usuarios_before_delete");
        $this->db->query("DROP TABLE IF EXISTS usuarios_historico");
        $this->db->query("DROP TABLE IF EXISTS usuarios");
    }
}

