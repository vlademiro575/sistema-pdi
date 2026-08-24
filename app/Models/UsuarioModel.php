<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Desativado: o SQLite gerencia a lixeira através de triggers BEFORE DELETE
    protected $useSoftDeletes   = false; 

    // Colunas autorizadas para operações de escrita (INSERT/UPDATE)
    protected $allowedFields    = [
        'nome', 
        'email', 
        'login', 
        'senha', 
        'perfil', 
        'ativo', 
        '_criado_por', 
        '_atualizado_por'
    ];

    // Configuração dos Timestamps nativos do CodeIgniter 4
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = '_criado_em';
    protected $updatedField     = '_atualizado_em';

    // Registro dos Eventos do Model (Callbacks)
    protected $beforeInsert     = ['preencherCriador'];
    protected $beforeUpdate     = ['preencherAtualizador'];

    /**
     * Callback disparado automaticamente antes de criar um novo usuário (INSERT)
     */
    protected function preencherCriador(array $data): array
    {
        // Obtém a sessão ativa do CodeIgniter
        $session = session();
        
        // Recupera o login armazenado na sessão (ex: setado após o login)
        // Se for uma operação CLI (como Seeds) ou cadastro público, define um padrão
        $usuarioLogado = $session->get('login') ?? 'sistema';

        // No CI4, os dados da entidade ficam encapsulados dentro da chave 'data'
        $data['data']['_criado_por']     = $usuarioLogado;
        $data['data']['_atualizado_por'] = $usuarioLogado;

        return $data;
    }

    /**
     * Callback disparado automaticamente antes de modificar um usuário (UPDATE)
     */
    protected function preencherAtualizador(array $data): array
    {
        $session = session();
        $usuarioLogado = $session->get('login') ?? 'sistema';

        // Atualiza apenas a autoria da modificação corrente
        $data['data']['_atualizado_por'] = $usuarioLogado;

        return $data;
    }
}
