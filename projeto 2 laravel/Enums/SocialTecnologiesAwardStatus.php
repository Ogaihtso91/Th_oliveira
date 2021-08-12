<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SocialTecnologiesAwardStatus extends Enum
{
    const EVALUATED = 'e';
    const CERTIFIED = 'c';
    const FINALIST = 'f';
    const WINNER = 'w';
}
