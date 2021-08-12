<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CustomStepFieldMask extends Enum
{
    const SemMascara =   0;
    const CPF =   1;
    const CNPJ = 2;
    const CEP = 3;
    const Dinheiro = 4;
}
