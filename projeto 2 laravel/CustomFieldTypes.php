<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomFieldTypes extends Model
{
    protected $table = 'custom_field_types';

    protected $fillable = [
        'name',
    ];

    public function customStepFields() {
        return $this->hasMany(CustomStepFields::class,'customFieldType_id','id');
    }
}
