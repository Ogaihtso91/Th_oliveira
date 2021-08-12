<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CategoryAward extends Model
{
    use SoftDeletes;

    protected $table = 'category_awards';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'categoryAwardType',
        'isCertificationType',
    ];

    protected $appends = [
        'count_social_tecnologies',
    ];

    protected $casts = [
        'isCertificationType' => 'boolean',
        'categoryAwardType' => 'integer',
    ];

    public function getCountSocialTecnologiesPrimaryThemesAttribute()
    {
        return DB::table('themes as t1')
            ->join('social_tecnologies_themes as t2','t1.id','=','t2.theme_id')
            ->join('social_tecnologies as t3','t3.id','=','t2.socialtecnology_id')
            ->join('category_award_social_tecnology as t4','t4.social_tecnology_id','=','t3.id')
            ->join('category_awards as t5','t5.id','=','t4.category_award_id')
            ->whereRaw('t5.id = ' . $this->id)
            ->whereRaw('t5.award_id = ' . $this->award_id)
            ->whereRaw('t2.is_primary_theme = ' . 1)
            ->groupBy('t1.id', 't1.name')
            ->selectRaw('t1.id, t1.name, count(*) as countSocialTecnologies')
            ->get()
        ;
    }
    public function getCountSocialTecnologiesSecondaryThemesAttribute()
    {
        return DB::table('themes as t1')
            ->join('social_tecnologies_themes as t2','t1.id','=','t2.theme_id')
            ->join('social_tecnologies as t3','t3.id','=','t2.socialtecnology_id')
            ->join('category_award_social_tecnology as t4','t4.social_tecnology_id','=','t3.id')
            ->join('category_awards as t5','t5.id','=','t4.category_award_id')
            ->whereRaw('t5.id = ' . $this->id)
            ->whereRaw('t5.award_id = ' . $this->award_id)
            ->whereRaw('t2.is_primary_theme = ' . 0)
            ->groupBy('t1.id', 't1.name')
            ->selectRaw('t1.id, t1.name, count(*) as countSocialTecnologies')
            ->get()
        ;
    }

    public function getCountSocialTecnologiesAttribute()
    {
        return $this->socialTecnologies()->count();
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function evaluationSteps()
    {
        return $this->hasMany(CategoryAwardEvaluationStep::class);
    }

    public function socialTecnologies()
    {
        return $this->belongsToMany(SocialTecnology::class)
            ->using(\App\CategoryAwardSocialTecnology::class)
            ->withPivot([
                'acceptTerms',
            ]);
    }

    public function stepForms()
    {
        return $this->belongsToMany(
            CustomStepForm::class, (new CategoryAwardCustomStepForms)->getTable(),
            'categoryAward_id',
            'customStepForm_id'
        )->withPivot([
            'wizard_step',
        ])->orderBy('wizard_step');
    }
}
