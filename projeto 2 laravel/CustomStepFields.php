<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SocialTecnologyFile;

class CustomStepFields extends Model
{
    protected $table = 'custom_step_fields';

    protected $fillable = [
        'title',
        'description',
        'mask',
        'isRequired',
        'customStepForm_id',
        'customFieldType_id',
        'status',
    ];

    public function fieldType() {
        return $this->belongsTo(CustomFieldTypes::class,'customFieldType_id','id');
    }

    public function stepForm() {
        return $this->belongsTo(CustomStepForm::class,'customStepForm_id','id');
    }

    public function socialTecnologyCustomStepFieldValues() {
        return $this->hasMany(SocialTecnologyCustomStepFieldValues::class,'customStepField_id','id');
    }

    /**
     * busca e retorna o valor preenchido de um campo especifico.
     *
     * @param integer $customForm_id
     * @param integer $socialTecnology_id
     * @return string - Valor já preenchido anteriormente pelo usuário.
     */
    public function socialTecnologyCustomStepFieldValue($customForm_id, $socialTecnology_id)
    {
        if(empty($this->socialTecnologyCustomStepFieldValues()->where('socialTecnology_id', $socialTecnology_id)->where('customStepForm_id', $customForm_id)->first())) {
            return null;
        } else {
            return $this->socialTecnologyCustomStepFieldValues()
                ->where('socialTecnology_id', $socialTecnology_id)
                ->where('customStepForm_id', $customForm_id)
                ->first()
                ->value;
        }
    }

    public function getAttachmentsFiles($customForm_id, $socialTecnology_id)
    {
        if(is_null($this->socialTecnologyCustomStepFieldValue($customForm_id, $socialTecnology_id))){
            return null;
        }

        $array_files_id = explode(',', $this->socialTecnologyCustomStepFieldValue($customForm_id, $socialTecnology_id) );

        $collect_SocialTecnologyFiles = collect([]);

        foreach($array_files_id as $file_id){
            if(!empty($file_id)){
                $collect_SocialTecnologyFiles->push(SocialTecnologyFile::find($file_id));
            }
        }

        return $collect_SocialTecnologyFiles;
    }

}
