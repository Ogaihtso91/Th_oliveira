<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyCustomStepFieldValues extends Model
{
    protected $table = 'socialTecnology_custom_step_fields_values';

    protected $fillable = [
        'socialTecnology_id',
        'customStepField_id',
        'customStepForm_id',
        'value',
        'fieldLabel',
    ];

    public function stepField()
    {
        return $this->belongsTo(CustomStepFields::class,'customStepField_id','id');
    }

    public function stepForm() {
        return $this->belongsTo(CustomStepForm::class,'customStepForm_id','id');
    }

    public function socialTecnology()
    {
        return $this->belongsTo(SocialTecnology::class,'socialTecnology_id','id');
    }
}
