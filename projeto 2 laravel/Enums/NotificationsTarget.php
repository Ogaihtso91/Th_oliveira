<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationsTarget extends Enum
{
    const USER = 0;
    const INSTITUTION = 1;
    const PROFILE = 2;
}
