<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationsSource extends Enum
{
    const Institution = 'institution';
    const Profile = 'profile';
    const User = 'user';
    const PublicProfile = 'public_profile';
    //const NewSource = 'source_string';
}
