<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Usuário Administrador
        $db->table('usuarios')->ignore(true)->insert([
            'nome'            => 'Administrador do Sistema',
            'email'           => 'admin@sistema.local',
            'login'           => 'admin',
            'senha'           => '$2y$12$RkOkfiUIeFFq7nQ9zfWD6eda2zKMaFnz63oPBH16VR.bugsGlItZ.', // '123456'
            'perfil'          => 'ADMIN',
            'ativo'           => 1,
            '_criado_em'      => date('Y-m-d H:i:s'),
            '_criado_por'     => 'sistema',
            '_atualizado_em'  => date('Y-m-d H:i:s'),
            '_atualizado_por' => 'sistema',
            '_operacao'       => 'INSERT'
        ]);

        // 2. Fundações
        $fundacoes = [
            [
                'sigla'           => 'FCPC',
                'nome'            => 'Fundação Cearense de Pesquisa e Cultura',
                'cnpj'            => '05.330.436/0001-62',
                'tipo'            => 'FUNDACAO_APOIO',
                '_criado_em'      => date('Y-m-d H:i:s'),
                '_criado_por'     => 'sistema',
                '_atualizado_em'  => date('Y-m-d H:i:s'),
                '_atualizado_por' => 'sistema',
                '_operacao'       => 'INSERT'
            ],
            [
                'sigla'           => 'FASTEF',
                'nome'            => 'Fundação de Apoio a Serviços Técnicos, Ensino e Fomento a Pesquisas',
                'cnpj'            => '08.918.421/0001-08',
                'tipo'            => 'FUNDACAO_APOIO',
                '_criado_em'      => date('Y-m-d H:i:s'),
                '_criado_por'     => 'sistema',
                '_atualizado_em'  => date('Y-m-d H:i:s'),
                '_atualizado_por' => 'sistema',
                '_operacao'       => 'INSERT'
            ]
        ];

        foreach ($fundacoes as $f) {
            $db->table('fundacoes')->ignore(true)->insert($f);
        }

        // 3. Professores
        $professores = [
            [
                'nome'            => 'Carlos Eduardo Almeida',
                'email'           => 'carlos.almeida@example.com',
                'telefone'        => '(85) 99911-2233',
                'cpf'             => '12345678901',
                'siape'           => '1234567',
                '_criado_em'      => '2026-08-01 08:30:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-01 08:30:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Maria Fernanda Souza',
                'email'           => 'maria.souza@example.com',
                'telefone'        => '(85) 99822-3344',
                'cpf'             => '23456789012',
                'siape'           => '1234568',
                '_criado_em'      => '2026-08-02 09:15:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-02 09:15:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'João Paulo Martins',
                'email'           => 'joao.martins@example.com',
                'telefone'        => '(85) 99733-4455',
                'cpf'             => '34567890123',
                'siape'           => '1234569',
                '_criado_em'      => '2026-08-03 10:00:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-03 10:00:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Ana Beatriz Oliveira',
                'email'           => 'ana.oliveira@example.com',
                'telefone'        => '(85) 99644-5566',
                'cpf'             => '45678901234',
                'siape'           => '1234570',
                '_criado_em'      => '2026-08-04 08:45:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-04 08:45:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Ricardo Henrique Costa',
                'email'           => 'ricardo.costa@example.com',
                'telefone'        => '(85) 99555-6677',
                'cpf'             => '56789012345',
                'siape'           => '1234571',
                '_criado_em'      => '2026-08-05 11:20:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-05 11:20:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Juliana Cristina Lima',
                'email'           => 'juliana.lima@example.com',
                'telefone'        => '(85) 99466-7788',
                'cpf'             => '67890123456',
                'siape'           => '1234572',
                '_criado_em'      => '2026-08-06 14:10:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-06 14:10:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Marcos Vinícius Rocha',
                'email'           => 'marcos.rocha@example.com',
                'telefone'        => '(85) 99377-8899',
                'cpf'             => '78901234567',
                'siape'           => '1234573',
                '_criado_em'      => '2026-08-07 09:50:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-07 09:50:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Patrícia Regina Mendes',
                'email'           => 'patricia.mendes@example.com',
                'telefone'        => '(85) 99288-9900',
                'cpf'             => '89012345678',
                'siape'           => '1234574',
                '_criado_em'      => '2026-08-08 13:25:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-08 13:25:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Felipe Augusto Nascimento',
                'email'           => 'felipe.nascimento@example.com',
                'telefone'        => '(85) 99199-0011',
                'cpf'             => '90123456789',
                'siape'           => '1234575',
                '_criado_em'      => '2026-08-09 15:40:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-09 15:40:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Luciana Alves Ferreira',
                'email'           => 'luciana.ferreira@example.com',
                'telefone'        => '(85) 99000-1122',
                'cpf'             => '01234567890',
                'siape'           => '1234576',
                '_criado_em'      => '2026-08-10 16:05:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-10 16:05:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ]
        ];

        foreach ($professores as $p) {
            $db->table('professores')->ignore(true)->insert($p);
        }

        // 4. Bolsistas
        $bolsistas = [
            [
                'nome'            => 'Amanda Caroline Silva',
                'cpf'             => '11223344556',
                'email'           => 'amanda.silva@example.com',
                'telefone'        => '(85) 98911-2233',
                'banco'           => 'Banco do Brasil',
                'agencia'         => '1234-5',
                'conta_corrente'  => '10001-2',
                '_criado_em'      => '2026-08-01 08:40:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-01 08:40:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Bruno Henrique Santos',
                'cpf'             => '22334455667',
                'email'           => 'bruno.santos@example.com',
                'telefone'        => '(85) 98822-3344',
                'banco'           => 'Caixa Econômica Federal',
                'agencia'         => '2345',
                'conta_corrente'  => '20002-3',
                '_criado_em'      => '2026-08-02 09:20:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-02 09:20:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Camila Vitória Oliveira',
                'cpf'             => '33445566778',
                'email'           => 'camila.oliveira@example.com',
                'telefone'        => '(85) 98733-4455',
                'banco'           => 'Banco do Brasil',
                'agencia'         => '3456-7',
                'conta_corrente'  => '30003-4',
                '_criado_em'      => '2026-08-03 10:15:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-03 10:15:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Daniel Lucas Pereira',
                'cpf'             => '44556677889',
                'email'           => 'daniel.pereira@example.com',
                'telefone'        => '(85) 98644-5566',
                'banco'           => 'Bradesco',
                'agencia'         => '4567',
                'conta_corrente'  => '40004-5',
                '_criado_em'      => '2026-08-04 11:00:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-04 11:00:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Eduarda Beatriz Costa',
                'cpf'             => '55667788990',
                'email'           => 'eduarda.costa@example.com',
                'telefone'        => '(85) 98555-6677',
                'banco'           => 'Santander',
                'agencia'         => '5678',
                'conta_corrente'  => '50005-6',
                '_criado_em'      => '2026-08-05 11:45:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-05 11:45:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Gabriel Augusto Martins',
                'cpf'             => '66778899001',
                'email'           => 'gabriel.martins@example.com',
                'telefone'        => '(85) 98466-7788',
                'banco'           => 'Nubank',
                'agencia'         => '0001',
                'conta_corrente'  => '60006-7',
                '_criado_em'      => '2026-08-06 13:10:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-06 13:10:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Isabela Cristina Rocha',
                'cpf'             => '77889900112',
                'email'           => 'isabela.rocha@example.com',
                'telefone'        => '(85) 98377-8899',
                'banco'           => 'Banco do Brasil',
                'agencia'         => '6789-0',
                'conta_corrente'  => '70007-8',
                '_criado_em'      => '2026-08-07 14:25:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-07 14:25:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Lucas Felipe Mendes',
                'cpf'             => '88990011223',
                'email'           => 'lucas.mendes@example.com',
                'telefone'        => '(85) 98288-9900',
                'banco'           => 'Itaú',
                'agencia'         => '7890',
                'conta_corrente'  => '80008-9',
                '_criado_em'      => '2026-08-08 15:30:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-08 15:30:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Mariana Alves Ferreira',
                'cpf'             => '99001122334',
                'email'           => 'mariana.ferreira@example.com',
                'telefone'        => '(85) 98199-0011',
                'banco'           => 'Caixa Econômica Federal',
                'agencia'         => '8901',
                'conta_corrente'  => '90009-0',
                '_criado_em'      => '2026-08-09 16:15:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-09 16:15:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ],
            [
                'nome'            => 'Rafael Vinícius Nascimento',
                'cpf'             => '10112233445',
                'email'           => 'rafael.nascimento@example.com',
                'telefone'        => '(85) 98000-1122',
                'banco'           => 'Banco do Brasil',
                'agencia'         => '9012-3',
                'conta_corrente'  => '10010-1',
                '_criado_em'      => '2026-08-10 17:00:00',
                '_criado_por'     => 'admin',
                '_atualizado_em'  => '2026-08-10 17:00:00',
                '_atualizado_por' => 'admin',
                '_operacao'       => 'INSERT'
            ]
        ];

        foreach ($bolsistas as $b) {
            $db->table('bolsistas')->ignore(true)->insert($b);
        }
    }
}

