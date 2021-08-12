<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AutoMessageTypes extends Enum
{
    const confirmarInscricao =   1;
    const inscricaoNaoFinalizada =   2;

    public static function getDescription($value): string
    {
    	if ($value === self::confirmarInscricao) {
    		return 'Confirmação de inscrição';
        }

    	if ($value === self::inscricaoNaoFinalizada) {
    		return 'Inscrição não finalizada';
        }

    	return parent::getDescription($value);
    }

}
