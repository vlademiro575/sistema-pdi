<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Se o usuário não estiver logado, barra o acesso e joga de volta na tela de login
        if (!session()->get('logado')) {
            return redirect()->to('login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Sem necessidade de manipulação após o carregamento da resposta
    }
}