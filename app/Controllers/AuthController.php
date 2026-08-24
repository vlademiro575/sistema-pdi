<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    /**
     * Renderiza a tela de login
     */
    public function login()
    {
        // Se o usuário já estiver logado, redireciona para a página principal
        if (session()->get('logado')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login');
    }

    /**
     * Processa a submissão das credenciais
     */
    public function autenticar()
    {
        $session = session();
        $model = new UsuarioModel();

        // Recupera dados enviados com proteção básica de input
        $loginInput = $this->request->getPost('login');
        $senhaInput = $this->request->getPost('senha');

        // Busca o usuário ativo no banco de dados SQLite
        $usuario = $model->where('login', $loginInput)
                         ->where('ativo', 1)
                         ->first();


        // Verifica a correspondência utilizando password_verify (Password Hashing do PHP)
        
        
        //Use esse trecho para depuração caso queira inspecionar o hash e a verificação da senha. 
        //Lembre-se de remover ou comentar antes de ir para produção.

        var_dump( password_verify($senhaInput, $usuario['senha']) );

        echo $senhaInput . '</br>';
        echo password_hash($senhaInput, PASSWORD_DEFAULT) .'</br>';
        echo $usuario['senha'] .'</br>';
        echo $usuario['login'] .'</br>';
        echo password_verify($senhaInput, $usuario['senha']) .'</br>';

      // die();
        
        if ($usuario && password_verify($senhaInput, $usuario['senha'])) {
            
            // Monta o payload ideal de Sessão
            $sessionData = [
                'id_usuario' => $usuario['id_usuario'],
                'nome'       => $usuario['nome'],
                'login'      => $usuario['login'], // Lembre-se: o UsuarioModel busca esta chave específica!
                'perfil'     => $usuario['perfil'],
                'logado'     => true
            ];

            $session->set($sessionData);

            // Redireciona o usuário para o painel principal
            return redirect()->to('dashboard');
        }

        // Caso as credenciais estejam incorretas
        $session->setFlashdata('erro', 'Usuário ou senha incorretos.');
        return redirect()->back()->withInput();
    }

    /**
     * Destrói a sessão e efetua o logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}