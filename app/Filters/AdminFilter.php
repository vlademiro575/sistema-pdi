<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Se o usuário não estiver logado, redireciona para a tela de login
        if (!session()->get('logado')) {
            return redirect()->to('login');
        }

        // Se o usuário não for ADMIN, bloqueia o acesso
        if (session()->get('perfil') !== 'ADMIN') {
            return redirect()->to('dashboard')->with('erro', 'Acesso negado! Apenas administradores podem acessar este módulo.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nenhuma ação necessária após a requisição
    }
}

