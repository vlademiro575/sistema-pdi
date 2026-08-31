<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDespesasTables extends Migration
{
    public function up()
    {
        // 1. Tabela Principal: despesas
        $this->db->query("
            CREATE TABLE IF NOT EXISTS despesas (
                id_despesa INTEGER PRIMARY KEY AUTOINCREMENT,
                id_projeto INTEGER NOT NULL,
                id_rubrica INTEGER NOT NULL,
                cnpj_fornecedor TEXT,
                nome_fornecedor TEXT,
                numero_nota TEXT,
                data_emissao DATE NOT NULL,
                valor_total REAL NOT NULL,
                descricao_itens TEXT,
                status_ocr TEXT DEFAULT 'PROCESSADO' CHECK (status_ocr IN ('PENDENTE', 'PROCESSADO', 'ERRO')),
                status_aprovacao TEXT DEFAULT 'EM_ANALISE' CHECK (status_aprovacao IN ('EM_ANALISE', 'APROVADO', 'REJEITADO')),
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT,
                FOREIGN KEY (id_projeto) REFERENCES projetos(id_projeto),
                FOREIGN KEY (id_rubrica) REFERENCES rubricas(id_rubrica)
            )
        ");

        // 2. Tabela de Histórico: despesas_historico
        $this->db->query("
            CREATE TABLE IF NOT EXISTS despesas_historico (
                id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
                id_despesa INTEGER NOT NULL,
                id_projeto INTEGER,
                id_rubrica INTEGER,
                cnpj_fornecedor TEXT,
                nome_fornecedor TEXT,
                numero_nota TEXT,
                data_emissao DATE,
                valor_total REAL,
                descricao_itens TEXT,
                status_ocr TEXT,
                status_aprovacao TEXT,
                _criado_em DATETIME,
                _criado_por TEXT,
                _atualizado_em DATETIME,
                _atualizado_por TEXT,
                _deletado_em DATETIME,
                _deletado_por TEXT,
                _operacao TEXT
            )
        ");

        // 3. Trigger AFTER INSERT (Debita Saldo da Rubrica e Gera Movimentação no Extrato)
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_despesa_after_insert
            AFTER INSERT ON despesas
            FOR EACH ROW
            BEGIN
                INSERT INTO movimentacoes_rubricas (
                    id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_por
                )
                SELECT 
                    NEW.id_rubrica, NEW.id_despesa, 'DESPESA', NEW.valor_total,
                    saldo_disponivel, saldo_disponivel - NEW.valor_total,
                    'Lançamento de despesa nº ' || COALESCE(NEW.numero_nota, 'S/N'), NEW._criado_por
                FROM rubricas WHERE id_rubrica = NEW.id_rubrica;

                UPDATE rubricas
                SET saldo_disponivel = saldo_disponivel - NEW.valor_total,
                    _atualizado_em = CURRENT_TIMESTAMP,
                    _atualizado_por = NEW._criado_por
                WHERE id_rubrica = NEW.id_rubrica;
            END
        ");

        // 4. Trigger BEFORE UPDATE (Auditoria + Ajuste de Saldo da Rubrica)
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_despesa_before_update
            BEFORE UPDATE ON despesas
            FOR EACH ROW
            BEGIN
                INSERT INTO despesas_historico (
                    id_despesa, id_projeto, id_rubrica, cnpj_fornecedor, nome_fornecedor,
                    numero_nota, data_emissao, valor_total, descricao_itens, status_ocr,
                    status_aprovacao, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao
                )
                VALUES (
                    OLD.id_despesa, OLD.id_projeto, OLD.id_rubrica, OLD.cnpj_fornecedor, OLD.nome_fornecedor,
                    OLD.numero_nota, OLD.data_emissao, OLD.valor_total, OLD.descricao_itens, OLD.status_ocr,
                    OLD.status_aprovacao, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao
                );

                UPDATE rubricas
                SET saldo_disponivel = saldo_disponivel + (OLD.valor_total - NEW.valor_total),
                    _atualizado_em = CURRENT_TIMESTAMP,
                    _atualizado_por = NEW._atualizado_por
                WHERE id_rubrica = NEW.id_rubrica
                  AND OLD.id_rubrica = NEW.id_rubrica
                  AND OLD.valor_total != NEW.valor_total;
            END
        ");

        // 5. Trigger BEFORE DELETE (Auditoria + Estorno de Saldo da Rubrica)
        $this->db->query("
            CREATE TRIGGER IF NOT EXISTS trigger_despesa_before_delete
            BEFORE DELETE ON despesas
            FOR EACH ROW
            BEGIN
                INSERT INTO despesas_historico (
                    id_despesa, id_projeto, id_rubrica, cnpj_fornecedor, nome_fornecedor,
                    numero_nota, data_emissao, valor_total, descricao_itens, status_ocr,
                    status_aprovacao, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao
                )
                VALUES (
                    OLD.id_despesa, OLD.id_projeto, OLD.id_rubrica, OLD.cnpj_fornecedor, OLD.nome_fornecedor,
                    OLD.numero_nota, OLD.data_emissao, OLD.valor_total, OLD.descricao_itens, OLD.status_ocr,
                    OLD.status_aprovacao, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao
                );

                INSERT INTO movimentacoes_rubricas (
                    id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_por
                )
                SELECT 
                    OLD.id_rubrica, OLD.id_despesa, 'ESTORNO', OLD.valor_total,
                    saldo_disponivel, saldo_disponivel + OLD.valor_total,
                    'Estorno por exclusão física da despesa', OLD._atualizado_por
                FROM rubricas WHERE id_rubrica = OLD.id_rubrica;

                UPDATE rubricas
                SET saldo_disponivel = saldo_disponivel + OLD.valor_total,
                    _atualizado_em = CURRENT_TIMESTAMP,
                    _atualizado_por = OLD._atualizado_por
                WHERE id_rubrica = OLD.id_rubrica;
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP TRIGGER IF EXISTS trigger_despesa_after_insert");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_despesa_before_update");
        $this->db->query("DROP TRIGGER IF EXISTS trigger_despesa_before_delete");
        $this->db->query("DROP TABLE IF EXISTS despesas_historico");
        $this->db->query("DROP TABLE IF EXISTS despesas");
    }
}

