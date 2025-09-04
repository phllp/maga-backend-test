<?php

if (!function_exists('format_cpf')) {
    function format_cpf(?string $cpf): string
    {
        // mantém apenas os dígitos preservando os zeros a frente
        $digits = preg_replace('/\D+/', '', (string)$cpf);

        if (strlen($digits) !== 11) {
            // se não tem o tamanho de um CPF retorna o original
            return (string)$cpf;
        }

        return preg_replace(
            '/(\d{3})(\d{3})(\d{3})(\d{2})/',
            '$1.$2.$3-$4',
            $digits
        );
    }
}
