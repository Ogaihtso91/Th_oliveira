<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Award extends Model
{
    use SoftDeletes;

    protected $table = 'awards';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id',
        'name',
        'description',
        'registrationsStartDate',
        'registrationsEndDate',
        'startDate',
        'endDate',
        'flagUniqueEntry',
        'terms',
        'acceptTerms',
        'allowForProfitSocialTechnologies',
    ];

    protected $casts = [
        'registrationsStartDate' => 'date',
        'registrationsEndDate' => 'date',
        'startDate' => 'date',
        'endDate' => 'date',
    ];

    protected $appends = [
        'total_social_tecnologies',
        'total_incomplete_subscriptions',
        'count_social_tecnologies_primary_themes',
        'count_social_tecnologies_secondary_themes',
    ];

    public function getTotalSocialTecnologiesAttribute()
    {
        return $this->getSocialTecnologiesContentManager(['status'=> 1])->count();
    }

    public function getTotalIncompleteSubscriptionsAttribute()
    {
        return $this->getSocialTecnologiesContentManager(['status'=> 0])->count();
    }

    public function getSocialTecnologiesContentManager(array $filters = null)
    {
        $contentManager = ContentManager::
            whereNotNull('wizard_step')
            ->whereNotNull('registration')
            ->whereDate('created_at','>=',$this->registrationsStartDate)
            ->whereDate('created_at','<=',$this->registrationsEndDate)
            ->where('type',1);

        $contentManager->whereExists(function($query){
            $query->select(DB::raw(1))
                ->from('social_tecnologies as st')
                ->whereRaw('st.award_id = ' . $this->id)
                ->whereRaw('st.id = content_manager.model_id ')
            ;
        });

        if(isset($filters['status']) && !is_null($filters['status'])){
            $contentManager->where('status',$filters['status']);
        }
        if(!empty($filters['startDate']) && !empty($filters['endDate'])){
            $contentManager->whereBetween('updated_at',[$filters['startDate'],$filters['endDate']]);
        }

        return $contentManager->get();
    }

    public function getCountSocialTecnologiesContentManagerGroupBy(array $filters = null)
    {
        // filtros para testes
        // TODO: retirar esses parametros de testes
        $filters = $filters ?? [
            'status' => 0,
            // 'UF' => 1,
            // 'primaryTheme' => '1',
            //  'secondaryTheme' => '1',
            'groupByCategaryAward' => 1,
        ];
        $contentManager = $this->getSocialTecnologiesContentManager($filters);

        if(!empty($filters['UF'])){
            $group = $contentManager->mapToGroups(function($item){
                return [
                    $item->institution->state => [
                        'institutionName' => $item->institution->institution_name,
                        'socialTecnologyName' => is_null($item->social_tecnology) ? ' - ' : $item->social_tecnology->socialtecnology_name,
                        'socialTecnologyRegistrationNumber' => is_null($item->social_tecnology) ? ' - ': $item->social_tecnology->registration,
                    ]
                ];
            })->map(function($item,$key){
                return [
                    'id' => $key,
                    'countSocialTecnologies' => count($item),
                ];
            });
            return $group;
        }
        if(!empty($filters['primaryTheme']) && $filters['primaryTheme'] == 1){
            $contentManager =  $contentManager->filter(function($item){
                return (!empty($item->social_tecnology->primaryTheme));
            });
            $group = $contentManager->mapToGroups(function($item){
                $primaryTheme = $item->social_tecnology->primaryTheme->first() ?? null;

                if(!empty($primaryTheme)) {
                    return [
                        $primaryTheme->id => [
                            'nameTheme' => $primaryTheme->name,
                        ]
                    ];
                } else {
                    return [
                        0 => [
                            'nameTheme' => '---',
                        ]
                    ];
                }
            })->map(function($item,$key){
                return [
                    'id' => $key,
                    'name' => $item->first()['nameTheme'],
                    'countSocialTecnologies' => count($item),
                ];
            });

            return $group;
        }

        if(!empty($filters['secondaryTheme']) && $filters['secondaryTheme'] == 1){
            $contentManager =  $contentManager->filter(function($item){
                return (!empty($item->social_tecnology->secondaryTheme));
            });
            $group = $contentManager->mapToGroups(function($item){
                $secondaryTheme = $item->social_tecnology->secondaryTheme->first() ?? null;
                $socialTecnology = $item->social_tecnology ?? null;

                if(!empty($secondaryTheme)) {
                    return [
                        $secondaryTheme->id => [
                            'nameTheme' => $secondaryTheme->name,
                            'socialTecnologyRegistrationNumber' => $socialTecnology->registration
                        ]
                    ];
                } else {
                    return [
                        0 => [
                            'nameTheme' => '---',
                            'socialTecnologyRegistrationNumber' => '---'
                        ]
                    ];
                }
            })->map(function($item,$key){
                return [
                    'id' => $key,
                    'name' => $item->first()['nameTheme'],
                    'countSocialTecnologies' => count($item),
                ];
            });
            return $group;
        }

        if(!empty($filters['groupByCategaryAward'])){
            $group = $contentManager->mapToGroups(function($item){
                $category = $item->social_tecnology->categoryAwardsSubscriptions->first() ?? null;

                if(!empty($category)) {
                    return [
                        $category->id => [
                            'category_award_name' => $category->name,
                        ]
                    ];
                } else {
                    return [
                        0 => [
                            'category_award_name' => '---',
                        ]
                    ];
                }
            })
            ->map(function($item,$key){
                return [
                    'id' => $key,
                    'categoryAwardName'  => $item->first()['category_award_name'],
                    'countSocialTecnologies' => count($item),
                ];
            })
            ;
            return $group;
        }
        return NULL;
    }

    public function getRegisteredSocialTecnologies()
    {
        // query de Tecnologias sociais inscritas em todas as categorias da premiação
        $querySocialTecnologies = SocialTecnology::whereExists(function($query){
            $query->select(DB::raw(1))
                ->from('category_award_social_tecnology as t2')
                ->join('category_awards as t3','t3.id','=','t2.category_award_id')
                ->whereRaw('t3.award_id = ' . $this->id)
                ->whereRaw('t2.social_tecnology_id = social_tecnologies.id');
            }
        )->whereNotExists(
            function($query){
                $query->select(DB::raw(1))
                ->from('content_manager as cm')
                ->whereNotNull('cm.registration')
                ->whereRaw('cm.status = 0')
                ->whereRaw('cm.type = 1')
                ->whereRaw('cm.model_id = social_tecnologies.id');
            }
        )
        ->get();

        // query de Tecnologias sociais com inscrições INCOMPLETAS em todas as categorias da premiação
        $queryContentManager = $this->getSocialTecnologiesContentManager(['status'=> 0]);

        // inicializando um objeto Colletction
        $registeredSocialTecnologies = collect([]);

        // iteração para adicionar as TS com inscrição completa na collection, formatado para a visão da dashboard
        foreach($querySocialTecnologies as $item){
            $registeredSocialTecnologies->push([
                'socialTecnology_id' => $item->id,
                'registration' => $item->registration,
                'socialTecnology_name' => $item->socialtecnology_name,
                'institution_name' => $item->institution->institution_name,
                'city' => $item->institution->city,
                'state' => $item->institution->state,
                'updated_at' => $item->updated_at,
                'wizard_step' => 11,
                'percent' => 100,
                'type' => 'SocialTecnology',
            ]);
        }

        // iteração para adicionar as TS com inscrição incompleta na collection, formatado para a visão da dashboard
        foreach($queryContentManager as $item){
            $registeredSocialTecnologies->push([
                'socialTecnology_id' => $item->model_id,
                'registration' => $item->registration,
                'socialTecnology_name' => $item->social_tecnology->socialtecnology_name ?? 'Erro inesperado - falha no contentManager',
                'institution_name' => $item->institution->institution_name,
                'city' => $item->institution->city,
                'state' => $item->institution->state,
                'updated_at' => $item->updated_at,
                'wizard_step' => $item->wizard_step,
                'percent' => ( $item->status == 0 and $item->wizard_step == 11 ) ? 99 : ( $item->wizard_step * 100 / 11 ),
                'type' => 'ContentManager',
            ]);
        }

        return $registeredSocialTecnologies;
    }

    public function getCountSocialTecnologiesPrimaryThemesAttribute()
    {
        return DB::table('themes as t1')
            ->join('social_tecnologies_themes as t2','t1.id','=','t2.theme_id')
            ->join('social_tecnologies as t3','t3.id','=','t2.socialtecnology_id')
            ->join('category_award_social_tecnology as t4','t4.social_tecnology_id','=','t3.id')
            ->join('category_awards as t5','t5.id','=','t4.category_award_id')
            ->whereRaw('t5.award_id = ' . $this->id)
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
            ->whereRaw('t5.award_id = ' . $this->id)
            ->whereRaw('t2.is_primary_theme = ' . 0)
            ->groupBy('t1.id', 't1.name')
            ->selectRaw('t1.id, t1.name, count(*) as countSocialTecnologies')
            ->get()
        ;
    }

    public function categoryAwards()
    {
        return $this->hasMany(CategoryAward::class, 'award_id', 'id');
    }

    public function socialTecnologies()
    {
        return $this->hasMany(SocialTecnology::class, 'award_id', 'id');
    }

    public function messages() {
        return $this->hasMany(AutoMessage::class);
    }

    public function files()
    {
        return $this->hasMany(AwardFile::class, 'award_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(AwardVideo::class, 'award_id', 'id');
    }
     public function Images()
    {
        return $this->hasMany(AwardImages::class, 'award_id', 'id');
    }

    public function mainImage()
    {
        return $this->images->where('main', 1)->first();
    }
}
