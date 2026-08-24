<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // 1. Mostra o caminho resolvido pela constante WRITEPATH
        echo "<b>Caminho WRITEPATH:</b> " . WRITEPATH . "<br>";

        // 2. Mostra a configuração exata do banco resolvida pelo CI4
        $dbConfig = config('Database')->default;
        
        echo "<pre>";
        echo "<b>Caminho do Banco resolvido:</b> " . $dbConfig['database'] . "\n";
        echo "<b>Arquivo existe fisicamente?</b> " . (file_exists($dbConfig['database']) ? 'SIM' : 'NÃO') . "\n";
        echo "<b>É gravável pelo PHP?</b> " . (is_writable(dirname($dbConfig['database'])) ? 'SIM' : 'NÃO') . "\n";
        echo "</pre>";
        
        die(); // Para a execução
        return view('welcome_message');

    }
}
