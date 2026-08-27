<?php

namespace Tests\Unit;

use App\Libraries\SefazXmlParser;
use CodeIgniter\Test\CIUnitTestCase;
use Exception;

final class SefazXmlParserTest extends CIUnitTestCase
{
    protected SefazXmlParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new SefazXmlParser();
    }

    public function testParseNfePadraoComNfeProc(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<nfeProc versao="4.00" xmlns="http://www.portalfiscal.inf.br/nfe">
    <NFe>
        <infNFe Id="NFe35230800000000000000550010000123451000123450" versao="4.00">
            <ide>
                <cUF>35</cUF>
                <cNF>00012345</cNF>
                <natOp>VENDA DE MERCADORIA</natOp>
                <mod>55</mod>
                <serie>1</serie>
                <nNF>12345</nNF>
                <dhEmi>2026-08-15T14:30:00-03:00</dhEmi>
                <tpNF>1</tpNF>
            </ide>
            <emit>
                <CNPJ>12345678000195</CNPJ>
                <xNome>DELL COMPUTADORES DO BRASIL LTDA</xNome>
                <xFant>DELL</xFant>
            </emit>
            <det nItem="1">
                <prod>
                    <cProd>NOTE001</cProd>
                    <xProd>Notebook Dell Latitude 3420</xProd>
                    <NCM>84713012</NCM>
                    <qCom>2.0000</qCom>
                    <vUnCom>4500.00</vUnCom>
                    <vProd>9000.00</vProd>
                </prod>
            </det>
            <det nItem="2">
                <prod>
                    <cProd>MOUS002</cProd>
                    <xProd>Mouse Optico Sem Fio Dell</xProd>
                    <NCM>84716053</NCM>
                    <qCom>2.0000</qCom>
                    <vUnCom>150.00</vUnCom>
                    <vProd>300.00</vProd>
                </prod>
            </det>
            <total>
                <ICMSTot>
                    <vProd>9300.00</vProd>
                    <vNF>9300.00</vNF>
                </ICMSTot>
            </total>
            <infAdic>
                <infCpl>Projeto PDI - Equipamentos para Laboratorio de IA</infCpl>
            </infAdic>
        </infNFe>
    </NFe>
</nfeProc>
XML;

        $resultado = $this->parser->parse($xml);

        $this->assertEquals('12345', $resultado['numero_nota']);
        $this->assertEquals('2026-08-15', $resultado['data_emissao']);
        $this->assertEquals('DELL COMPUTADORES DO BRASIL LTDA', $resultado['nome_fornecedor']);
        $this->assertEquals('12.345.678/0001-95', $resultado['cnpj_fornecedor']);
        $this->assertEquals(9300.00, $resultado['valor_total']);
        $this->assertStringContainsString('Notebook Dell Latitude 3420', $resultado['descricao_itens']);
        $this->assertStringContainsString('Mouse Optico Sem Fio Dell', $resultado['descricao_itens']);
        $this->assertStringContainsString('Informações Complementares: Projeto PDI', $resultado['descricao_itens']);
    }

    public function testParseNfeSemWrapperNfeProc(): void
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<NFe xmlns="http://www.portalfiscal.inf.br/nfe">
    <infNFe Id="NFe35230800000000000000550010000999991000999990" versao="4.00">
        <ide>
            <nNF>99999</nNF>
            <dEmi>2026-05-10</dEmi>
        </ide>
        <emit>
            <CPF>12345678901</CPF>
            <xNome>MARIA PRESTADORA DE SERVICOS</xNome>
        </emit>
        <det nItem="1">
            <prod>
                <xProd>Consultoria Tecnica Especializada</xProd>
                <qCom>1</qCom>
                <vUnCom>1500.00</vUnCom>
                <vProd>1500.00</vProd>
            </prod>
        </det>
        <total>
            <ICMSTot>
                <vNF>1500.00</vNF>
            </ICMSTot>
        </total>
    </infNFe>
</NFe>
XML;

        $resultado = $this->parser->parse($xml);

        $this->assertEquals('99999', $resultado['numero_nota']);
        $this->assertEquals('2026-05-10', $resultado['data_emissao']);
        $this->assertEquals('MARIA PRESTADORA DE SERVICOS', $resultado['nome_fornecedor']);
        $this->assertEquals('123.456.789-01', $resultado['cnpj_fornecedor']);
        $this->assertEquals(1500.00, $resultado['valor_total']);
        $this->assertStringContainsString('Consultoria Tecnica Especializada', $resultado['descricao_itens']);
    }

    public function testParseXmlInvalidoLancaExcecao(): void
    {
        $this->expectException(Exception::class);
        $this->parser->parse('<invalido>conteudo que nao e nfe</invalido>');
    }

    public function testParseXmlVazioLancaExcecao(): void
    {
        $this->expectException(Exception::class);
        $this->parser->parse('');
    }
}

