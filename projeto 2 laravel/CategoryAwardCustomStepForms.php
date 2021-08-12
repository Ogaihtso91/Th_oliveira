<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryAwardCustomStepForms extends Pivot
{
    protected $table = 'category_award_custom_step_forms';

    protected $fillable = [
        'categoryAward_id',
        'customStepForm_id',
        'wizard_step',
    ];
}
