-- Desativa a verificação de chaves estrangeiras para permitir o DROP sem erros de dependência
PRAGMA foreign_keys = OFF;

-- ============================================================================
-- 1. LIMPEZA INICIAL DE TABELAS (REEXECUÇÃO SEGURA)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1.1 TRIGGERS DE UPDATE
-- ----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS trigger_usuarios_before_update;
DROP TRIGGER IF EXISTS trigger_bolsistas_before_update;
DROP TRIGGER IF EXISTS trigger_professores_before_update;
DROP TRIGGER IF EXISTS trigger_fundacoes_before_update;
DROP TRIGGER IF EXISTS trigger_projetos_before_update;
DROP TRIGGER IF EXISTS trigger_rubricas_before_update;
DROP TRIGGER IF EXISTS trigger_projetos_bolsistas_before_update;
DROP TRIGGER IF EXISTS trigger_movimentacoes_rubricas_before_update;
DROP TRIGGER IF EXISTS trigger_despesa_before_update;
DROP TRIGGER IF EXISTS trigger_anexos_before_update;
DROP TRIGGER IF EXISTS trigger_logs_ocr_before_update;
DROP TRIGGER IF EXISTS trigger_historico_status_despesas_before_update;


-- ----------------------------------------------------------------------------
-- 1.2 TRIGGERS DE DELETE
-- ----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS trigger_usuarios_before_delete;
DROP TRIGGER IF EXISTS trigger_bolsistas_before_delete;
DROP TRIGGER IF EXISTS trigger_professores_before_delete;
DROP TRIGGER IF EXISTS trigger_fundacoes_before_delete;
DROP TRIGGER IF EXISTS trigger_projetos_before_delete;
DROP TRIGGER IF EXISTS trigger_rubricas_before_delete;
DROP TRIGGER IF EXISTS trigger_projetos_bolsistas_before_delete;
DROP TRIGGER IF EXISTS trigger_movimentacoes_rubricas_before_delete;
DROP TRIGGER IF EXISTS trigger_despesa_before_delete;
DROP TRIGGER IF EXISTS trigger_anexos_before_delete;
DROP TRIGGER IF EXISTS trigger_logs_ocr_before_delete;
DROP TRIGGER IF EXISTS trigger_historico_status_despesas_before_delete;


-- ----------------------------------------------------------------------------
-- 1.3 TRIGGERS ESPECÍFICOS DAS DESPESAS
-- ----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS trigger_despesa_after_insert;


-- ----------------------------------------------------------------------------
-- 1.4 EXCLUSÃO DE TODAS AS TABELAS PRINCIPAIS
-- ----------------------------------------------------------------------------
-- As tabelas dependentes são incluídas também para permitir a
-- reexecução completa e segura do script.

DROP TABLE IF EXISTS historico_status_despesas;
DROP TABLE IF EXISTS logs_ocr;
DROP TABLE IF EXISTS anexos;
DROP TABLE IF EXISTS despesas;
DROP TABLE IF EXISTS movimentacoes_rubricas;
DROP TABLE IF EXISTS projetos_bolsistas;
DROP TABLE IF EXISTS rubricas;
DROP TABLE IF EXISTS projetos;
DROP TABLE IF EXISTS fundacoes;
DROP TABLE IF EXISTS professores;
DROP TABLE IF EXISTS bolsistas;
DROP TABLE IF EXISTS usuarios;


-- ----------------------------------------------------------------------------
-- 1.5 EXCLUSÃO DAS TABELAS DE HISTÓRICO
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS historico_status_despesas_historico;
DROP TABLE IF EXISTS logs_ocr_historico;
DROP TABLE IF EXISTS anexos_historico;
DROP TABLE IF EXISTS despesas_historico;
DROP TABLE IF EXISTS movimentacoes_rubricas_historico;
DROP TABLE IF EXISTS projetos_bolsistas_historico;
DROP TABLE IF EXISTS rubricas_historico;
DROP TABLE IF EXISTS projetos_historico;
DROP TABLE IF EXISTS fundacoes_historico;
DROP TABLE IF EXISTS professores_historico;
DROP TABLE IF EXISTS bolsistas_historico;
DROP TABLE IF EXISTS usuarios_historico;


-- ----------------------------------------------------------------------------
-- 1.6 REATIVA A VERIFICAÇÃO DE CHAVES ESTRANGEIRAS
-- ----------------------------------------------------------------------------

PRAGMA foreign_keys = ON;

-- ============================================================================
-- 2. USUÁRIOS
-- ============================================================================
CREATE TABLE usuarios (
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
);

CREATE TABLE usuarios_historico (
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
);

CREATE TRIGGER trigger_usuarios_before_update BEFORE UPDATE ON usuarios FOR EACH ROW BEGIN
    INSERT INTO usuarios_historico (id_usuario, nome, email, login, senha, perfil, ativo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_usuario, OLD.nome, OLD.email, OLD.login, OLD.senha, OLD.perfil, OLD.ativo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_usuarios_before_delete BEFORE DELETE ON usuarios FOR EACH ROW BEGIN
    INSERT INTO usuarios_historico (id_usuario, nome, email, login, senha, perfil, ativo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_usuario, OLD.nome, OLD.email, OLD.login, OLD.senha, OLD.perfil, OLD.ativo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;


-- ============================================================================
-- 3. BOLSISTAS
-- ============================================================================
CREATE TABLE bolsistas (
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
);

CREATE TABLE bolsistas_historico (
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
);

CREATE TRIGGER trigger_bolsistas_before_update BEFORE UPDATE ON bolsistas FOR EACH ROW BEGIN
    INSERT INTO bolsistas_historico (id_bolsista, id_usuario, nome, cpf, email, telefone, banco, agencia, conta_corrente, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao )
    VALUES (OLD.id_bolsista, OLD.id_usuario, OLD.nome, OLD.cpf, OLD.email, OLD.telefone, OLD.banco, OLD.agencia, OLD.conta_corrente, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por , OLD._operacao );
END;

CREATE TRIGGER trigger_bolsistas_before_delete BEFORE DELETE ON bolsistas FOR EACH ROW BEGIN
    INSERT INTO bolsistas_historico (id_bolsista, id_usuario, nome, cpf, email, telefone, banco, agencia, conta_corrente, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_bolsista, OLD.id_usuario, OLD.nome, OLD.cpf, OLD.email, OLD.telefone, OLD.banco, OLD.agencia, OLD.conta_corrente, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por,  OLD._operacao );
END;

-- ============================================================================
-- 4. PROFESSORES
-- ============================================================================

CREATE TABLE professores (
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
);

CREATE TABLE professores_historico (
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
);

CREATE TRIGGER trigger_professores_before_update BEFORE UPDATE ON professores FOR EACH ROW BEGIN
    INSERT INTO professores_historico (id_professor, id_usuario, nome, email, telefone, cpf, siape, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
    VALUES (OLD.id_professor, OLD.id_usuario, OLD.nome, OLD.email, OLD.telefone, OLD.cpf, OLD.siape, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_professores_before_delete BEFORE DELETE ON professores FOR EACH ROW BEGIN
    INSERT INTO professores_historico (id_professor, id_usuario, nome, email, telefone, cpf, siape, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_professor, OLD.id_usuario, OLD.nome, OLD.email, OLD.telefone, OLD.cpf, OLD.siape, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, 'DELETE');
END;

-- ============================================================================
-- 5. FUNDAÇÕES
-- ============================================================================
CREATE TABLE fundacoes (
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
);

CREATE TABLE fundacoes_historico (
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
);

CREATE TRIGGER trigger_fundacoes_before_update BEFORE UPDATE ON fundacoes FOR EACH ROW BEGIN
    INSERT INTO fundacoes_historico (id_fundacao, sigla, nome, cnpj, tipo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
    VALUES (OLD.id_fundacao, OLD.sigla, OLD.nome, OLD.cnpj, OLD.tipo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_fundacoes_before_delete BEFORE DELETE ON fundacoes FOR EACH ROW BEGIN
    INSERT INTO fundacoes_historico (id_fundacao, sigla, nome, cnpj, tipo, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_fundacao, OLD.sigla, OLD.nome, OLD.cnpj, OLD.tipo, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao);
END;

-- ============================================================================
-- 6. PROJETOS
-- ============================================================================
CREATE TABLE projetos (
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
);

CREATE TABLE projetos_historico (
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
);

CREATE TRIGGER trigger_projetos_before_update BEFORE UPDATE ON projetos FOR EACH ROW BEGIN
    INSERT INTO projetos_historico (id_projeto, id_professor, id_fundacao, codigo_projeto_fundacao, titulo, orcamento_total, data_inicio, data_fim, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
    VALUES (OLD.id_projeto, OLD.id_professor, OLD.id_fundacao, OLD.codigo_projeto_fundacao, OLD.titulo, OLD.orcamento_total, OLD.data_inicio, OLD.data_fim, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_projetos_before_delete BEFORE DELETE ON projetos FOR EACH ROW BEGIN
    INSERT INTO projetos_historico (id_projeto, id_professor, id_fundacao, codigo_projeto_fundacao, titulo, orcamento_total, data_inicio, data_fim, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao )
    VALUES (OLD.id_projeto, OLD.id_professor, OLD.id_fundacao, OLD.codigo_projeto_fundacao, OLD.titulo, OLD.orcamento_total, OLD.data_inicio, OLD.data_fim, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;

-- ============================================================================
-- 7. RUBRICAS
-- ============================================================================
CREATE TABLE rubricas (
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
    _operacao TEXT ,
    FOREIGN KEY (id_projeto) REFERENCES projetos(id_projeto)
);

CREATE TABLE rubricas_historico (
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
);

CREATE TRIGGER trigger_rubricas_before_update BEFORE UPDATE ON rubricas FOR EACH ROW BEGIN
    INSERT INTO rubricas_historico (id_rubrica, id_projeto, nome, tipo, valor_alocado, saldo_disponivel, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
    VALUES (OLD.id_rubrica, OLD.id_projeto, OLD.nome, OLD.tipo, OLD.valor_alocado, OLD.saldo_disponivel, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_rubricas_before_delete BEFORE DELETE ON rubricas FOR EACH ROW BEGIN
    INSERT INTO rubricas_historico (id_rubrica, id_projeto, nome, tipo, valor_alocado, saldo_disponivel, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_rubrica, OLD.id_projeto, OLD.nome, OLD.tipo, OLD.valor_alocado, OLD.saldo_disponivel, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;

-- ============================================================================
-- 8. PROJETOS_BOLSISTAS (VÍNCULO)
-- ============================================================================
CREATE TABLE projetos_bolsistas (
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
);

CREATE TABLE projetos_bolsistas_historico (
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
);

CREATE TRIGGER trigger_projetos_bolsistas_before_update BEFORE UPDATE ON projetos_bolsistas FOR EACH ROW BEGIN
    INSERT INTO projetos_bolsistas_historico (id_projeto_bolsista, id_projeto, id_bolsista, valor_bolsa, data_inicio, data_fim, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
    VALUES (OLD.id_projeto_bolsista, OLD.id_projeto, OLD.id_bolsista, OLD.valor_bolsa, OLD.data_inicio, OLD.data_fim, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_projetos_bolsistas_before_delete BEFORE DELETE ON projetos_bolsistas FOR EACH ROW BEGIN
    INSERT INTO projetos_bolsistas_historico (id_projeto_bolsista, id_projeto, id_bolsista, valor_bolsa, data_inicio, data_fim, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_projeto_bolsista, OLD.id_projeto, OLD.id_bolsista, OLD.valor_bolsa, OLD.data_inicio, OLD.data_fim, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;


-- ============================================================================
-- 9. MOVIMENTAÇÕES DE RUBRICA (EXTRATO)
-- ============================================================================

CREATE TABLE movimentacoes_rubricas (
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
);

CREATE TABLE movimentacoes_rubricas_historico (
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
);

CREATE TRIGGER trigger_movimentacoes_rubricas_before_update BEFORE UPDATE ON movimentacoes_rubricas FOR EACH ROW BEGIN
    INSERT INTO movimentacoes_rubricas_historico (id_movimentacao_rubrica, id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_em, _criado_por, _atualizado_em, _atualizado_por,  _operacao)
    VALUES (OLD.id_movimentacao_rubrica, OLD.id_rubrica, OLD.id_despesa, OLD.tipo, OLD.valor, OLD.saldo_anterior, OLD.saldo_posterior, OLD.descricao, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_movimentacoes_rubricas_before_delete BEFORE DELETE ON movimentacoes_rubricas FOR EACH ROW BEGIN
    INSERT INTO movimentacoes_rubricas_historico (id_movimentacao_rubrica, id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_movimentacao_rubrica, OLD.id_rubrica, OLD.id_despesa, OLD.tipo, OLD.valor, OLD.saldo_anterior, OLD.saldo_posterior, OLD.descricao, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por,  OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;


-- ============================================================================
-- 10. DESPESAS (COM REGRAS DE NEGÓCIO E CONTROLE DE SALDO DA RUBRICA)
-- ============================================================================
CREATE TABLE despesas (
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
    _atualizado_em DATETIME ,
    _atualizado_por TEXT,
    _deletado_em DATETIME,
    _deletado_por TEXT,
    _operacao TEXT,
    FOREIGN KEY (id_projeto) REFERENCES projetos(id_projeto),
    FOREIGN KEY (id_rubrica) REFERENCES rubricas(id_rubrica)
);

CREATE TABLE despesas_historico (
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
);

-- Trigger 10.1: Inserção -> Debita Saldo e Registra no Extrato (Regra de Negócio, NÃO é histórico)
CREATE TRIGGER trigger_despesa_after_insert
AFTER INSERT ON despesas
FOR EACH ROW
BEGIN
    -- Lança a movimentação no extrato da rubrica
    INSERT INTO movimentacoes_rubricas (
        id_rubrica, id_despesa, tipo, valor, saldo_anterior, saldo_posterior, descricao, _criado_por
    )
    SELECT 
        NEW.id_rubrica, NEW.id_despesa, 'DESPESA', NEW.valor_total,
        saldo_disponivel, saldo_disponivel - NEW.valor_total,
        'Lançamento de despesa nº ' || COALESCE(NEW.numero_nota, 'S/N'), NEW._criado_por
    FROM rubricas WHERE id_rubrica = NEW.id_rubrica;

    -- Atualiza saldo da rubrica
    UPDATE rubricas
    SET saldo_disponivel = saldo_disponivel - NEW.valor_total,
        _atualizado_em = CURRENT_TIMESTAMP,
        _atualizado_por = NEW._criado_por
    WHERE id_rubrica = NEW.id_rubrica;
END;

-- Trigger 10.2: Atualização -> Grava Histórico ('UPDATE') e Ajusta Saldo da Rubrica
CREATE TRIGGER trigger_despesa_before_update
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
END;

-- Trigger 10.3: Exclusão -> Grava Histórico ('DELETE') e Estorna Saldo no Extrato
CREATE TRIGGER trigger_despesa_before_delete
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
END;

-- ============================================================================
-- 11. ANEXOS DA DESPESA
-- ============================================================================
CREATE TABLE anexos (
    id_anexo INTEGER PRIMARY KEY AUTOINCREMENT,
    id_despesa INTEGER NOT NULL,
    nome_arquivo TEXT NOT NULL,
    tipo TEXT NOT NULL CHECK (tipo IN ('NOTA_FISCAL', 'BOLETO', 'COMPROVANTE_PIX', 'COMPROVANTE_TED', 'FOTO', 'PDF', 'OUTRO')),
    url TEXT NOT NULL,
    _criado_em DATETIME ,
    _criado_por TEXT,
    _atualizado_em DATETIME,
    _atualizado_por TEXT,
    _deletado_em DATETIME,
    _deletado_por TEXT,
    _operacao TEXT,
    FOREIGN KEY (id_despesa) REFERENCES despesas(id_despesa)
);

CREATE TABLE anexos_historico (
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
);

CREATE TRIGGER trigger_anexos_before_update BEFORE UPDATE ON anexos FOR EACH ROW BEGIN
    INSERT INTO anexos_historico (id_anexo, id_despesa, nome_arquivo, tipo, url, _criado_em, _criado_por, _atualizado_em, _atualizado_por,  _operacao)
    VALUES (OLD.id_anexo, OLD.id_despesa, OLD.nome_arquivo, OLD.tipo, OLD.url, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao);
END;

CREATE TRIGGER trigger_anexos_before_delete BEFORE DELETE ON anexos FOR EACH ROW BEGIN
    INSERT INTO anexos_historico (id_anexo, id_despesa, nome_arquivo, tipo, url, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_anexo, OLD.id_despesa, OLD.nome_arquivo, OLD.tipo, OLD.url, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao);
END;

-- ============================================================================
-- 12. LOGS OCR
-- ============================================================================
CREATE TABLE logs_ocr (
    id_log_ocr INTEGER PRIMARY KEY AUTOINCREMENT,
    id_despesa INTEGER NOT NULL,
    motor TEXT NOT NULL,
    texto_extraido TEXT,
    confianca REAL,
    tempo_execucao REAL,
    status TEXT DEFAULT 'SUCESSO' CHECK (status IN ('SUCESSO', 'ERRO', 'PARCIAL')),
    _criado_em DATETIME ,
    _criado_por TEXT,
    _atualizado_em DATETIME ,
    _atualizado_por TEXT,
    _deletado_em DATETIME,
    _deletado_por TEXT,
    FOREIGN KEY (id_despesa) REFERENCES despesas(id_despesa)
);

CREATE TABLE logs_ocr_historico (
    id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
    id_log_ocr INTEGER NOT NULL,
    id_despesa INTEGER,
    motor TEXT,
    texto_extraido TEXT,
    confianca REAL,
    tempo_execucao REAL,
    status TEXT,
    _criado_em DATETIME,
    _criado_por TEXT,
    _atualizado_em DATETIME,
    _atualizado_por TEXT,
    _deletado_em DATETIME,
    _deletado_por TEXT,
    _operacao TEXT
);

CREATE TRIGGER trigger_logs_ocr_before_update BEFORE UPDATE ON logs_ocr FOR EACH ROW BEGIN
    INSERT INTO logs_ocr_historico (id_log_ocr, id_despesa, motor, texto_extraido, confianca, tempo_execucao, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _operacao)
    VALUES (OLD.id_log_ocr, OLD.id_despesa, OLD.motor, OLD.texto_extraido, OLD.confianca, OLD.tempo_execucao, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao );
END;

CREATE TRIGGER trigger_logs_ocr_before_delete BEFORE DELETE ON logs_ocr FOR EACH ROW BEGIN
    INSERT INTO logs_ocr_historico (id_log_ocr, id_despesa, motor, texto_extraido, confianca, tempo_execucao, status, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_log_ocr, OLD.id_despesa, OLD.motor, OLD.texto_extraido, OLD.confianca, OLD.tempo_execucao, OLD.status, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por,  OLD._deletado_em, OLD._deletado_por, OLD._operacao );
END;

-- ============================================================================
-- 13. HISTÓRICO DE STATUS DA DESPESA
-- ============================================================================
CREATE TABLE historico_status_despesas (
    id_historico_status INTEGER PRIMARY KEY AUTOINCREMENT,
    id_despesa INTEGER NOT NULL,
    status_anterior TEXT,
    status_novo TEXT NOT NULL,
    justificativa TEXT,
    _criado_em DATETIME ,
    _criado_por TEXT,
    _atualizado_em DATETIME ,
    _atualizado_por TEXT,
    _deletado_em DATETIME,
    _deletado_por TEXT,
    _operacao TEXT,
    FOREIGN KEY (id_despesa) REFERENCES despesas(id_despesa)
);

CREATE TABLE historico_status_despesas_historico (
    id_historico INTEGER PRIMARY KEY AUTOINCREMENT,
    id_historico_status INTEGER NOT NULL,
    id_despesa INTEGER,
    status_anterior TEXT,
    status_novo TEXT,
    justificativa TEXT,
    _criado_em DATETIME,
    _criado_por TEXT,
    _atualizado_em DATETIME,
    _atualizado_por TEXT,
    _deletado_em DATETIME,
    _deletado_por TEXT,
    _operacao TEXT
);

CREATE TRIGGER trigger_historico_status_despesas_before_update BEFORE UPDATE ON historico_status_despesas FOR EACH ROW BEGIN
    INSERT INTO historico_status_despesas_historico (id_historico_status, id_despesa, status_anterior, status_novo, justificativa, _criado_em, _criado_por, _atualizado_em, _atualizado_por,  _operacao)
    VALUES (OLD.id_historico_status, OLD.id_despesa, OLD.status_anterior, OLD.status_novo, OLD.justificativa, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._operacao);
END;

CREATE TRIGGER trigger_historico_status_despesas_before_delete BEFORE DELETE ON historico_status_despesas FOR EACH ROW BEGIN
    INSERT INTO historico_status_despesas_historico (id_historico_status, id_despesa, status_anterior, status_novo, justificativa, _criado_em, _criado_por, _atualizado_em, _atualizado_por, _deletado_em, _deletado_por, _operacao)
    VALUES (OLD.id_historico_status, OLD.id_despesa, OLD.status_anterior, OLD.status_novo, OLD.justificativa, OLD._criado_em, OLD._criado_por, OLD._atualizado_em, OLD._atualizado_por, OLD._deletado_em, OLD._deletado_por, OLD._operacao);
END;


-- ============================================================================
-- 13. SEEDER / CARGA INICIAL: USUÁRIO ADMINISTRADOR
-- ============================================================================
INSERT INTO usuarios (
    nome, 
    email, 
    login, 
    senha, 
    perfil, 
    ativo, 
    _criado_em, 
    _criado_por, 
    _atualizado_em, 
    _atualizado_por
) VALUES (
    'Administrador do Sistema',
    'admin@sistema.local',
    'admin',
    '$2y$12$RkOkfiUIeFFq7nQ9zfWD6eda2zKMaFnz63oPBH16VR.bugsGlItZ.', -- Hash Bcrypt (custo/cost 12) para a senha '123456'
    'ADMIN',
    1,
    CURRENT_TIMESTAMP,
    'sistema',
    CURRENT_TIMESTAMP,
    'sistema'
);

-- Insert para a FCPC (Fundação Cearense de Pesquisa e Cultura)
INSERT INTO fundacoes (
    sigla, 
    nome, 
    cnpj, 
    tipo, 
    _criado_em, 
    _criado_por, 
    _atualizado_em, 
    _atualizado_por, 
    _operacao
) VALUES (
    'FCPC', 
    'Fundação Cearense de Pesquisa e Cultura', 
    '05.330.436/0001-62', 
    'FUNDACAO_APOIO', 
    CURRENT_TIMESTAMP, 
    'sistema', 
    CURRENT_TIMESTAMP, 
    'sistema', 
    'INSERT'
);

-- Insert para a FASTEF (Fundação de Apoio a Serviços Técnicos, Ensino e Fomento a Pesquisas)
INSERT INTO fundacoes (
    sigla, 
    nome, 
    cnpj, 
    tipo, 
    _criado_em, 
    _criado_por, 
    _atualizado_em, 
    _atualizado_por, 
    _operacao
) VALUES (
    'FASTEF', 
    'Fundação de Apoio a Serviços Técnicos, Ensino e Fomento a Pesquisas', 
    '08.918.421/0001-08', 
    'FUNDACAO_APOIO', 
    CURRENT_TIMESTAMP, 
    'sistema', 
    CURRENT_TIMESTAMP, 
    'sistema', 
    'INSERT'
);

-- Professores
INSERT INTO professores (
    nome, email, telefone, cpf, siape,
    _criado_em, _criado_por, _atualizado_em, _atualizado_por,
    _deletado_em, _deletado_por, _operacao
) VALUES
('Carlos Eduardo Almeida', 'carlos.almeida@example.com', '(85) 99911-2233', '12345678901', '1234567',
 '2026-08-01 08:30:00', 'admin', '2026-08-01 08:30:00', 'admin', NULL, NULL, 'INSERT'),

('Maria Fernanda Souza', 'maria.souza@example.com', '(85) 99822-3344', '23456789012', '1234568',
 '2026-08-02 09:15:00', 'admin', '2026-08-02 09:15:00', 'admin', NULL, NULL, 'INSERT'),

('João Paulo Martins', 'joao.martins@example.com', '(85) 99733-4455', '34567890123', '1234569',
 '2026-08-03 10:00:00', 'admin', '2026-08-03 10:00:00', 'admin', NULL, NULL, 'INSERT'),

('Ana Beatriz Oliveira', 'ana.oliveira@example.com', '(85) 99644-5566', '45678901234', '1234570',
 '2026-08-04 08:45:00', 'admin', '2026-08-04 08:45:00', 'admin', NULL, NULL, 'INSERT'),

('Ricardo Henrique Costa', 'ricardo.costa@example.com', '(85) 99555-6677', '56789012345', '1234571',
 '2026-08-05 11:20:00', 'admin', '2026-08-05 11:20:00', 'admin', NULL, NULL, 'INSERT'),

('Juliana Cristina Lima', 'juliana.lima@example.com', '(85) 99466-7788', '67890123456', '1234572',
 '2026-08-06 14:10:00', 'admin', '2026-08-06 14:10:00', 'admin', NULL, NULL, 'INSERT'),

('Marcos Vinícius Rocha', 'marcos.rocha@example.com', '(85) 99377-8899', '78901234567', '1234573',
 '2026-08-07 09:50:00', 'admin', '2026-08-07 09:50:00', 'admin', NULL, NULL, 'INSERT'),

('Patrícia Regina Mendes', 'patricia.mendes@example.com', '(85) 99288-9900', '89012345678', '1234574',
 '2026-08-08 13:25:00', 'admin', '2026-08-08 13:25:00', 'admin', NULL, NULL, 'INSERT'),

('Felipe Augusto Nascimento', 'felipe.nascimento@example.com', '(85) 99199-0011', '90123456789', '1234575',
 '2026-08-09 15:40:00', 'admin', '2026-08-09 15:40:00', 'admin', NULL, NULL, 'INSERT'),

('Luciana Alves Ferreira', 'luciana.ferreira@example.com', '(85) 99000-1122', '01234567890', '1234576',
 '2026-08-10 16:05:00', 'admin', '2026-08-10 16:05:00', 'admin', NULL, NULL, 'INSERT');

-- Bolsistas
    INSERT INTO bolsistas (
    nome, cpf, email, telefone, banco, agencia, conta_corrente,
    _criado_em, _criado_por, _atualizado_em, _atualizado_por,
    _deletado_em, _deletado_por, _operacao
) VALUES
('Amanda Caroline Silva', '11223344556', 'amanda.silva@example.com', '(85) 98911-2233',
 'Banco do Brasil', '1234-5', '10001-2',
 '2026-08-01 08:40:00', 'admin', '2026-08-01 08:40:00', 'admin', NULL, NULL, 'INSERT'),

('Bruno Henrique Santos', '22334455667', 'bruno.santos@example.com', '(85) 98822-3344',
 'Caixa Econômica Federal', '2345', '20002-3',
 '2026-08-02 09:20:00', 'admin', '2026-08-02 09:20:00', 'admin', NULL, NULL, 'INSERT'),

('Camila Vitória Oliveira', '33445566778', 'camila.oliveira@example.com', '(85) 98733-4455',
 'Banco do Brasil', '3456-7', '30003-4',
 '2026-08-03 10:15:00', 'admin', '2026-08-03 10:15:00', 'admin', NULL, NULL, 'INSERT'),

('Daniel Lucas Pereira', '44556677889', 'daniel.pereira@example.com', '(85) 98644-5566',
 'Bradesco', '4567', '40004-5',
 '2026-08-04 11:00:00', 'admin', '2026-08-04 11:00:00', 'admin', NULL, NULL, 'INSERT'),

('Eduarda Beatriz Costa', '55667788990', 'eduarda.costa@example.com', '(85) 98555-6677',
 'Santander', '5678', '50005-6',
 '2026-08-05 11:45:00', 'admin', '2026-08-05 11:45:00', 'admin', NULL, NULL, 'INSERT'),

('Gabriel Augusto Martins', '66778899001', 'gabriel.martins@example.com', '(85) 98466-7788',
 'Nubank', '0001', '60006-7',
 '2026-08-06 13:10:00', 'admin', '2026-08-06 13:10:00', 'admin', NULL, NULL, 'INSERT'),

('Isabela Cristina Rocha', '77889900112', 'isabela.rocha@example.com', '(85) 98377-8899',
 'Banco do Brasil', '6789-0', '70007-8',
 '2026-08-07 14:25:00', 'admin', '2026-08-07 14:25:00', 'admin', NULL, NULL, 'INSERT'),

('Lucas Felipe Mendes', '88990011223', 'lucas.mendes@example.com', '(85) 98288-9900',
 'Itaú', '7890', '80008-9',
 '2026-08-08 15:30:00', 'admin', '2026-08-08 15:30:00', 'admin', NULL, NULL, 'INSERT'),

('Mariana Alves Ferreira', '99001122334', 'mariana.ferreira@example.com', '(85) 98199-0011',
 'Caixa Econômica Federal', '8901', '90009-0',
 '2026-08-09 16:15:00', 'admin', '2026-08-09 16:15:00', 'admin', NULL, NULL, 'INSERT'),

('Rafael Vinícius Nascimento', '10112233445', 'rafael.nascimento@example.com', '(85) 98000-1122',
 'Banco do Brasil', '9012-3', '10010-1',
 '2026-08-10 17:00:00', 'admin', '2026-08-10 17:00:00', 'admin', NULL, NULL, 'INSERT');
