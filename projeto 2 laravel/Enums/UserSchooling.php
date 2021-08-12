<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserSchooling extends Enum
{
    const Ensinofundamental = 'F';
    const Ensinomedio = 'M';
    const Superior = 'S';
    const Posgraduacao = 'P';

    public static function getDescription($value): string
    {
    	if ($value === self::Ensinofundamental) {
    		return 'Ensino Fundamental';
    	}

    	if ($value === self::Ensinomedio) {
    		return 'Ensino Médio';
    	}

    	if ($value === self::Superior) {
    		return 'Superior';
    	}

    	if ($value === self::Posgraduacao) {
    		return 'Pós-Graduação';
    	}

    	return parent::getDescription($value);
    }
}