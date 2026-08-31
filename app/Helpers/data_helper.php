<?php

if (!function_exists('formatar_data')) {
    /**
     * Formata uma data para o padrão brasileiro dd/mm/yyyy
     *
     * @param string|null $data Data em formato aceito por strtotime (ex: 2026-08-31)
     * @param string $fallback Valor retornado caso a data seja vazia ou inválida
     * @return string Data formatada em dd/mm/yyyy
     */
    function formatar_data(?string $data, string $fallback = '-'): string
    {
        if (empty($data) || $data === '0000-00-00' || $data === '0000-00-00 00:00:00') {
            return $fallback;
        }

        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return $fallback;
        }

        return date('d/m/Y', $timestamp);
    }
}

if (!function_exists('formatar_data_hora')) {
    /**
     * Formata data e hora para o padrão dd/mm/yyyy às H:i:s (ou H:i)
     *
     * @param string|null $data Data e hora (ex: 2026-08-31 14:30:00)
     * @param bool $incluirSegundos Se true exibe com segundos, senão apenas até minutos
     * @param string $fallback Valor retornado caso a data seja vazia ou inválida
     * @return string Data e hora formatadas
     */
    function formatar_data_hora(?string $data, bool $incluirSegundos = true, string $fallback = '-'): string
    {
        if (empty($data) || $data === '0000-00-00' || $data === '0000-00-00 00:00:00') {
            return $fallback;
        }

        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return $fallback;
        }

        $formato = $incluirSegundos ? 'd/m/Y \à\s H:i:s' : 'd/m/Y H:i';
        return date($formato, $timestamp);
    }
}

if (!function_exists('converter_data_iso')) {
    /**
     * Converte data no formato dd/mm/yyyy para o formato ISO yyyy-mm-dd (para inserção em banco)
     *
     * @param string|null $dataBr Data no formato dd/mm/yyyy
     * @return string|null Data em yyyy-mm-dd ou null
     */
    function converter_data_iso(?string $dataBr): ?string
    {
        if (empty($dataBr)) {
            return null;
        }

        $partes = explode('/', trim($dataBr));
        if (count($partes) === 3 && checkdate((int)$partes[1], (int)$partes[0], (int)$partes[2])) {
            return sprintf('%04d-%02d-%02d', $partes[2], $partes[1], $partes[0]);
        }

        return $dataBr;
    }
}

