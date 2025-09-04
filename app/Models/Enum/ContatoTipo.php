<?php

namespace App\Models\Enum;

enum ContatoTipo: int
{
    case EMAIL = 1;
    case TELEFONE = 2;

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::TELEFONE => 'Telefone',
        };
    }
}
