<?php
namespace App\Services;

class OcrService
{
    /**
     * Simula a leitura de um comprovante fiscal
     */
    public function processar(string $caminhoArquivo): array
    {
        // Simula o tempo de processamento de uma rede neural (1 a 2 segundos)
        sleep(rand(1, 2));

        // Gera uma porcentagem de confiança aleatória para testarmos a barra de cores na View
        $confianca = rand(45, 98);

        $status = $confianca >= 50 ? 'SUCESSO' : 'ERRO';

        return [
            'motor'          => 'Vision AI V1.0 (Mock Engine)',
            'texto_extraido' => "RAZÃO SOCIAL: FORNECEDOR MOCK LTDA\nCNPJ: 12.345.678/0001-99\nDATA DE EMISSÃO: " . date('d/m/Y') . "\nVALOR TOTAL: R$ 1.500,00\n\n[FIM DO COMPROVANTE]",
            'confianca'      => $confianca,
            'tempo_execucao' => rand(100, 350) / 100, // Simula o tempo gasto (ex: 1.54s)
            'status'         => $status
        ];
    }
}