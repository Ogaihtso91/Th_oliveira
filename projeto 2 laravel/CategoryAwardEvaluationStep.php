<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\UserAdmin;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CategoryAwardEvaluationStep extends Model
{
    use SoftDeletes;

    protected $table = 'category_award_evaluation_steps';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'evaluationType',
        'enableTwoEvaluators',
        'evaluator_role_id',
        'validator_role_id',
        'awardStatusForApprovedSocialTecnology',
        'numberOfApprovedSocialTechnologies',
        'previousEvaluationStep_id',
        'status',
        'approvedListPublished_flag',
        'approvedList_published_at',
    ];

    protected $appends = [
        'approved_social_tecnolgies',
    ];

    protected $casts = [
        'numberOfApprovedSocialTechnologies' => 'integer',
        'evaluator_role_id' => 'integer',
        'validator_role_id' => 'integer',
        'previousEvaluationStep_id' => 'integer',
        'status' => 'integer',
        'approvedListPublished_flag' => 'boolean',
        'approvedList_published_at' => 'datetime',
    ];

    public function categoryAward()
    {
        return $this->belongsTo(CategoryAward::class);
    }

    public function evaluationCriteria()
    {
        return $this->hasMany(CategoryAwardEvaluationCriterion::class,'evaluationStep_id','id');
    }

    public function evaluators()
    {
        return $this->hasMany(CategoryAwardEvaluationStepEvaluators::class,'evaluationStep_id','id');
    }

    public function socialTecnologyEvaluationStep()
    {
        return $this->hasMany(SocialTecnologyCategoryAwardEvaluationStep::class,'evaluationStep_id','id');
    }

    public function socialTecnologyEvaluationCriteria()
    {
        return $this->hasMany(SocialTecnologyCategoryAwardEvaluationCriteria::class,'evaluationStep_id','id');
    }

    public function socialTecnologyEvaluation()
    {
        return $this->hasMany(SocialTecnologyEvaluatorStepEvaluation::class,'evaluationStep_id','id');
    }

    public function getSocialTecnologyStepScore()
    {
        return $this->socialTecnologyEvaluationStep->sortByDesc('final_score_preview');
    }

    public function validateApprovedSocialTechnologies()
    {
        $i = 1;
        foreach($this->getSocialTecnologyStepScore() as $item){

            if($i <= $this->numberOfApprovedSocialTechnologies){
                $item->update(['validated' => 1]);
                if($this->categoryAward->isCertificationType && strtoupper($this->awardStatusForApprovedSocialTecnology) == 'F' ){
                    $item->socialTecnology->update(['award_status' => $this->awardStatusForApprovedSocialTecnology]);
                }elseif(!$this->categoryAward->isCertificationType){
                    $item->socialTecnology->update(['challenge_award_status' => $this->awardStatusForApprovedSocialTecnology]);
                }
            }else{
                $item->update(['validated' => 0]);
            }
            $i++;
        }
    }

    public function validateSocialTechnologiesStepEvaluationScore()
    {
        foreach($this->getSocialTecnologyStepScore() as $item){
            $item->setFinalStepScore();
        }
    }

    public function getEvaluatorRole()
    {
        return Role::find($this->evaluator_role_id);
    }
    public function getAuditorRole()
    {
        return Role::find($this->auditor_role_id);
    }

    public function previousEvaluationStep()
    {
        return CategoryAwardEvaluationStep::find($this->previousEvaluationStep_id) ?? 0;
    }

    public function getApprovedSocialTecnolgiesAttribute()
    {
        return $this->getApprovedSocialTecnolgies()->get();
    }

    public function getApprovedSocialTecnolgies()
    {
        return
            SocialTecnology::whereHas('evaluationsStep', function ($query){
                $query->where('evaluationStep_id',$this->id);
                $query->where('validated',1);
            });
    }

    public function queryToDashboard(array $filters = null)
    {
        // TODO: retirar esse filtro de testes
        // para testes
        $filters = $filters ?? [
            'validated' => 0,
            'evaluated' => 0,
            // 'is_primary_theme' => 0,
            'state' => 1,
        ];
        $query = DB::table( (new SocialTecnology())->getTable() . ' as t1')
            ->join( (new Institution())->getTable() .' as t5','t5.id','=','t1.institution_id');

        if(isset($filters['is_primary_theme'])){
            $query = $query->join( (new SocialTecnologyTheme())->getTable() . ' as t2','t1.id','=','t2.socialTecnology_id');
            $query = $query->join( (new Theme())->getTable() .' as t3','t3.id','=','t2.theme_id');
        }

        $query->whereExists(
            function ($query) use ($filters){
                $query->selectRaw('1');
                $query->from('category_award_social_tecnology as txx');
                $query->whereRaw('txx.social_tecnology_id = t1.id');
                $query->whereRaw('txx.category_award_id = ' . $this->category_award_id);

            }
        );

        $query->whereExists(
            function ($query) use ($filters){
                $query->selectRaw('1')
                ->from((new SocialTecnologyCategoryAwardEvaluationStep())->getTable() . ' as t4')
                ->whereRaw('t4.evaluationStep_id = ' . $this->id)
                ->whereRaw('t4.socialTecnology_id = t1.id'
                );
                if( isset($filters['validated']) && $filters['validated'] == 1) {
                    $query->whereNotNull('t4.validated');
                }elseif( isset($filters['validated']) && $filters['validated'] == 0) {
                    $query->whereNull('t4.validated');
                }
            }
        );

        if(isset($filters['evaluated']) && $filters['evaluated'] == 1){
            $query->whereExists(
                function ($query) use ($filters){
                    $query->selectRaw('1')
                    ->from((new SocialTecnologyEvaluatorStepEvaluation())->getTable() . ' as t4')
                    ->whereRaw('t4.evaluationStep_id = ' . $this->id)
                    ->whereRaw('t4.socialTecnology_id = t1.id'
                    );
                }
            );
        }elseif(isset($filters['evaluated']) && $filters['evaluated'] == 0){
            $query->whereNotExists(
                function ($query) use ($filters){
                    $query->selectRaw('1')
                    ->from((new SocialTecnologyEvaluatorStepEvaluation())->getTable() . ' as t4')
                    ->whereRaw('t4.evaluationStep_id = ' . $this->id)
                    ->whereRaw('t4.socialTecnology_id = t1.id'
                    );
                }
            );
        }

        if(!empty($filters['state'])) {
            $query->groupBy('t5.state');
            $query->selectRaw('t5.state, count(*) as countSocialTecnologies');
        }

        if(isset($filters['is_primary_theme'])){
            $query->whereRaw('t2.is_primary_theme = ' . $filters['is_primary_theme']);
            $query->groupBy('t3.name');
            $query->selectRaw('t3.name as nameTheme, count(*) as countSocialTecnologies');
        }

        return $query->get();
    }

    public function getValidatorRole()
    {
        return Role::find($this->validator_role_id);
    }

    public function getListEvaluatorsFree()
    {
        return
            (! is_null($this->getEvaluatorRole()))
            ? UserAdmin::whereNotExists(
                function ($query)
                {
                    $query->select(DB::raw(1))
                        ->from('evaluation_step_evaluators as t1')
                        ->join('evaluation_step_evaluators_users_admin as t2','t1.id','=','t2.evaluation_step_evaluators_id')
                        ->whereRaw('t1.status = 1')
                        ->whereRaw('t1.evaluationStep_id = ' . $this->id )
                        ->whereRaw('t2.evaluator_id = users_admin.id');
                }
            )
            ->role($this->getEvaluatorRole()->name ?? '')
            ->get()
            : NULL;
    }


    public function getActiveEvaluators(){
        return $this->evaluators->where('status',1);
    }

    public function getEvaluators ()
    {
        return CategoryAwardEvaluationStepEvaluatorUserAdmin::whereHas('categoryAwardEvaluationStepEvaluators',
            function ($query) {
                $query->where('evaluationStep_id', $this->id)
                    ->where('status', 1);
            }
        )
        ->with(['evaluator', 'categoryAwardEvaluationStepEvaluators'])
        ->get();
    }

    public function getActiveEvaluatorsWithoutSocialTecnologies(){
        return $this->getActiveEvaluators()
                ->where('count_social_tecnologies',0);
    }

    public function getSocialTecnologyWithoutEvaluators()
    {
        return $this->categoryAward->socialTecnologies()
                ->whereDoesntHave('evaluationCriteria')
                ->whereDoesntHave('evaluations')
                ->whereDoesntHave('evaluationsStep')
                ->orWhereHas('evaluationsStep',
                    function ($query) {
                        $query->where('status',0);
                    }
                )
                ->toSql();
    }

    public function getSocialTecnologyWithInactiveEvaluators()
    {
        return $this->socialTecnologyEvaluationStep()
                ->whereNull('validated')
                ->whereHas('evaluationStepEvaluators',
                    function ($query) {
                        $query->where('status',0);
                    })
                ->get();
    }

    public function getSocialTecnolgiesForDistribuitionToEvaluators()
    {
        return (is_null($this->previousEvaluationStep_id))
            ? $this->categoryAward
                ->socialTecnologies()
                ->where('status',1)
                ->whereHas('manager',function($query){
                    $query->where('status',1);
                    // $query->where('wizard_step', '>=' , 11);
                })
                ->whereDoesntHave('evaluations', function ($query){
                    $query->where('evaluationStep_id',$this->id);
                })->get()
            : $this->previousEvaluationStep()
                ->getApprovedSocialTecnolgies()
                ->whereDoesntHave('evaluations', function ($query){
                    $query->where('evaluationStep_id',$this->id);
                })->get()
            ;
    }

    public function distributeSocialTechnologiesToEvaluators ()
    {

        // // dd( $this->getActiveEvaluators() ,  $this->getSocialTecnolgiesForDistribuitionToEvaluators()  );
        // if($this->getActiveEvaluatorsWithoutSocialTecnologies()->count() > 0 || $this->getSocialTecnolgiesForDistribuitionToEvaluators()->count() > 0)
        // {
        //     $this->socialTecnologyEvaluationStep()
        //         ->whereDoesntHave('evaluationStepEvaluators', function ($query) {
        //             $query->where('evaluationStep_id',$this->id);
        //         })
        //         ->whereNull('validated')
        //         ->delete();

        //     $ts = $this->getSocialTecnolgiesForDistribuitionToEvaluators()->shuffle()->toArray();
        //     $evaluators = $this->getActiveEvaluators()->shuffle()->toArray();
        //     $countEvaluators = $this->getActiveEvaluatorsWithoutSocialTecnologies()->count();
        if($this->getActiveEvaluatorsWithoutSocialTecnologies()->count() > 0)
        {
            $this->socialTecnologyEvaluationStep()->whereNull('validated')->delete();

            $ts = $this->getSocialTecnolgiesForDistribuitionToEvaluators()->shuffle()->toArray();
            $evaluators = $this->getActiveEvaluators()->shuffle()->toArray();
            $countEvaluators = $this->getActiveEvaluatorsWithoutSocialTecnologies()->count();

            $auxEvaluators=0;
            foreach($ts as $key => $value){
                $arr = [
                    'evaluationStep_id' => $this->id,
                    'socialTecnology_id' => $value['id'],
                    'evaluation_step_evaluators_id' => $evaluators[$auxEvaluators]['id'],
                ];
                $auxEvaluators = ($auxEvaluators+1 == $countEvaluators) ? 0 : ++$auxEvaluators;
                SocialTecnologyCategoryAwardEvaluationStep::create($arr);
            }

            return $this->socialTecnologyEvaluationStep;
        }
        return false;
    }

}
