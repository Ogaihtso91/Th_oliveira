<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstitutionUser extends Model
{
    protected $table = 'institution_user';

    protected $fillable = [
        'user_id',
        'institution_id'
    ];
}
