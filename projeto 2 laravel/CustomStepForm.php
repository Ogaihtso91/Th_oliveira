<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomStepForm extends Model
{
    protected $table = 'custom_step_forms';

    protected $fillable = [
        'name',
        'title',
        'status',
    ];

    public function stepFields()
    {
        return $this->hasMany(CustomStepFields::class,'customStepForm_id','id');
    }

    public function categoryAwards()
    {
        return $this->belongsToMany(
            CategoryAward::class, (new CategoryAwardCustomStepForms)->getTable(),'customStepForm_id','categoryAward_id'
        )->withPivot([
            'wizard_step',
        ]);
    }

    public function activeStepFields ()
    {
        return $this->stepFields()->where('status', 1)->orderBy('field_position')->get();
    }
}
