<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryAwardSocialTecnology extends Pivot
{
    protected $table = 'category_award_social_tecnology';

    protected $fillable = [
        'category_award_id',
        'social_tecnology_id',
        'acceptTerms',
    ];
}
