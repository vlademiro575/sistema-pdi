<?php

namespace App\Libraries;

use Exception;
use SimpleXMLElement;

class SefazXmlParser
{
    /**
     * Faz o parse do conteúdo XML de uma NF-e / NFC-e padrão SEFAZ
     *
     * @param string $xmlContent Conteúdo bruto do arquivo XML
     * @return array Dados estruturados extraídos da nota fiscal
     * @throws Exception Caso o XML seja inválido ou não seja padrão SEFAZ
     */
    public function parse(string $xmlContent): array
    {
        if (empty(trim($xmlContent))) {
            throw new Exception('O arquivo XML enviado está vazio.');
        }

        // Habilita tratamento interno de erros da libxml
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $msg = !empty($errors) ? $errors[0]->message : 'Estrutura XML malformada.';
            throw new Exception('Erro ao processar o arquivo XML: ' . trim($msg));
        }

        // Busca o nó infNFe usando XPath tolerante a namespaces
        $infNFeNodes = $xml->xpath('//*[local-name()="infNFe"]');
        if (empty($infNFeNodes)) {
            throw new Exception('O arquivo XML não possui a tag <infNFe>. Certifique-se de que é uma NF-e ou NFC-e padrão SEFAZ.');
        }

        $infNFe = $infNFeNodes[0];

        // 1. Dados de Identificação (ide)
        $ideNodes = $infNFe->xpath('./*[local-name()="ide"]');
        $ide = !empty($ideNodes) ? $ideNodes[0] : null;

        $numeroNota = '';
        $dataEmissao = date('Y-m-d');

        if ($ide) {
            $nNF = $ide->xpath('./*[local-name()="nNF"]');
            if (!empty($nNF)) {
                $numeroNota = (string) $nNF[0];
            }

            // dhEmi (data/hora no padrão 2023-08-15T10:00:00-03:00) ou dEmi (2023-08-15)
            $dhEmi = $ide->xpath('./*[local-name()="dhEmi"]');
            $dEmi  = $ide->xpath('./*[local-name()="dEmi"]');

            if (!empty($dhEmi)) {
                $dataRaw = (string) $dhEmi[0];
                $dataEmissao = substr($dataRaw, 0, 10);
            } elseif (!empty($dEmi)) {
                $dataRaw = (string) $dEmi[0];
                $dataEmissao = substr($dataRaw, 0, 10);
            }
        }

        // 2. Dados do Emitente / Fornecedor (emit)
        $emitNodes = $infNFe->xpath('./*[local-name()="emit"]');
        $emit = !empty($emitNodes) ? $emitNodes[0] : null;

        $nomeFornecedor = '';
        $cnpjFornecedor = '';

        if ($emit) {
            $xNome = $emit->xpath('./*[local-name()="xNome"]');
            if (!empty($xNome)) {
                $nomeFornecedor = (string) $xNome[0];
            } else {
                $xFant = $emit->xpath('./*[local-name()="xFant"]');
                if (!empty($xFant)) {
                    $nomeFornecedor = (string) $xFant[0];
                }
            }

            $cnpj = $emit->xpath('./*[local-name()="CNPJ"]');
            $cpf  = $emit->xpath('./*[local-name()="CPF"]');

            if (!empty($cnpj)) {
                $cnpjFornecedor = $this->formatarCnpjCpf((string) $cnpj[0]);
            } elseif (!empty($cpf)) {
                $cnpjFornecedor = $this->formatarCnpjCpf((string) $cpf[0]);
            }
        }

        // 3. Dados Totais (total -> ICMSTot -> vNF)
        $totalNodes = $infNFe->xpath('./*[local-name()="total"]/*[local-name()="ICMSTot"]/*[local-name()="vNF"]');
        $valorTotal = 0.00;

        if (!empty($totalNodes)) {
            $valorTotal = (float) ((string) $totalNodes[0]);
        } else {
            // Fallback para outros nós de total se necessário
            $vNFTodos = $infNFe->xpath('//*[local-name()="vNF"]');
            if (!empty($vNFTodos)) {
                $valorTotal = (float) ((string) $vNFTodos[0]);
            }
        }

        // 4. Detalhamento de Produtos / Serviços (det)
        $detNodes = $infNFe->xpath('./*[local-name()="det"]');
        $itensDescricao = [];
        $contadorItem = 1;

        foreach ($detNodes as $det) {
            $prodNodes = $det->xpath('./*[local-name()="prod"]');
            if (!empty($prodNodes)) {
                $prod = $prodNodes[0];
                $xProd  = (string) ($prod->xpath('./*[local-name()="xProd"]')[0] ?? 'Item sem descrição');
                $qCom   = (float)  ($prod->xpath('./*[local-name()="qCom"]')[0] ?? 1);
                $vUnCom = (float)  ($prod->xpath('./*[local-name()="vUnCom"]')[0] ?? 0);
                $vProd  = (float)  ($prod->xpath('./*[local-name()="vProd"]')[0] ?? 0);

                $qComFormatado   = (floor($qCom) == $qCom) ? number_format($qCom, 0, ',', '.') : number_format($qCom, 2, ',', '.');
                $vUnFormatado    = number_format($vUnCom, 2, ',', '.');
                $vProdFormatado  = number_format($vProd, 2, ',', '.');

                $itensDescricao[] = "Item {$contadorItem}: {$xProd} (Qtd: {$qComFormatado} | Vl. Unit: R$ {$vUnFormatado} | Total: R$ {$vProdFormatado})";
                $contadorItem++;
            }
        }

        // 5. Informações Complementares (infAdic -> infCpl)
        $infCplNodes = $infNFe->xpath('./*[local-name()="infAdic"]/*[local-name()="infCpl"]');
        if (!empty($infCplNodes)) {
            $infCpl = trim((string) $infCplNodes[0]);
            if (!empty($infCpl)) {
                $itensDescricao[] = "\nInformações Complementares: " . $infCpl;
            }
        }

        $descricaoItens = implode("\n", $itensDescricao);

        return [
            'numero_nota'      => $numeroNota,
            'data_emissao'     => $dataEmissao,
            'nome_fornecedor'  => $nomeFornecedor,
            'cnpj_fornecedor'  => $cnpjFornecedor,
            'valor_total'      => $valorTotal,
            'descricao_itens'  => $descricaoItens,
            'status_aprovacao' => 'EM_ANALISE'
        ];
    }

    /**
     * Formata uma string numérica de CNPJ (14 dígitos) ou CPF (11 dígitos)
     */
    protected function formatarCnpjCpf(string $doc): string
    {
        $doc = preg_replace('/\D/', '', $doc);

        if (strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }

        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }

        return $doc;
    }
}

