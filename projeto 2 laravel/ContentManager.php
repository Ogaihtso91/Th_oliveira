<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContentManager extends Model
{
    /*********** CONSTANTS ***********/
	const TYPE_SOCIALTECNOLOGY = 1;
	const TYPE_EVENT = 2;
	const TYPE_INSTITUTION = 3;
	const TYPE_SOCIALTECNOLOGY_COMMENT = 4;
    const TYPE_BLOG_COMMENT = 5;

    const STATUS_PENDING = 0;
    const STATUS_COMPLETE = 1;

    /*********** PARAMETERS ***********/
    protected $table = 'content_manager';

    protected $fillable = [
        'user_id',
    	'user_admin_id',
    	'institution_id',
    	'model_id',
    	'type',
    	'old_values',
    	'new_values',
        'note',
        'reverted',
        'is_admin',
        'registration',
        'status',
        'wizard_step'
    ];

    protected $appends = array(
        'date_string',
        'array_old_values',
        'array_new_values',
        'social_tecnology',
    );

    /*********** RELATIONS ***********/
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function user_admin()
    {
        return $this->belongsTo(UserAdmin::class, 'user_admin_id')->withTrashed();
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /*********** GET CUSTOM PARAMETERS ***********/
    public function getSocialTecnologyAttribute()
    {
        return ($this->type == $this::TYPE_SOCIALTECNOLOGY) ? SocialTecnology::find($this->model_id) : NULL;
    }

    public function getDateStringAttribute()
    {
        return \Date::parse($this->created_at)->format('d \d\e F');
    }

    public function getArrayOldValuesAttribute()
    {
        return json_decode($this->old_values);
    }

    public function getArrayNewValuesAttribute()
    {
        return json_decode($this->new_values);
    }

    public function getValueAwardId()
    {
        return $this->getArrayNewValuesAttribute()->award_id;
    }

    /**
     * Build Timeline Query
     * @param Array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function query_to_timeline($filters) {

        $cm_obj = self::query();

        // 4762 - limitar TS's da Linha do tempo
        // cliente solicitou que na linha do tempo do gestor apareça somente TS's certificadas++
        $cm_obj->where(function($query){
            $query->where('type',self::TYPE_SOCIALTECNOLOGY);
            $query->whereNull('wizard_step');
            $query->whereExists(
                function($query){
                    $query->select(\DB::raw(1))
                          ->from( (new SocialTecnology)->getTable() . ' as  TS'  )
                          ->whereRaw('content_manager.model_id = TS.id')
                          ->whereNotNull('TS.award_status');
                }
            );
        });
        // 4762 - fim

        if(!empty($filters['grupo'])) $cm_obj->where('type', $filters['grupo']);
        if(!empty($filters['instituicao'])) $cm_obj->where('institution_id', $filters['instituicao']);
        if(!empty($filters['start_date'])) {
            $where_start_date = Carbon::createFromFormat('d/m/Y H:i', $filters['start_date']." 00:00");
            $cm_obj->where('created_at', '>=', $where_start_date);
        }
        if(!empty($filters['end_date'])) {
            $where_end_date = Carbon::createFromFormat('d/m/Y H:i', $filters['end_date']." 23:59");
            $cm_obj->where('created_at', '<=', $where_end_date);
        }
        return $cm_obj->orderBy('created_at','desc')->paginate(10)->groupBy('date_string');
    }

    /**
     * Mark the notification as read.
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     * @return void
     */
    public function markAsUnread()
    {
        if (! is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function store($data) {
        try {
            return ContentManager::create([
                'user_id' => Auth::guard()->user()->id,
                'institution_id' => $data['institution_id'],
                'model_id' => $data['model_id'],
                'type' => $data['type'],
                'old_values' => $data['old_values'] ?? null,
                'new_values' => json_encode($data['new_values']),
                'registration' => $data['registration'],
                'status' => $data['status'] ?? ContentManager::STATUS_PENDING,
                'wizard_step' => $data['wizard_step'] ?? null,
            ]);
        }
        catch(Exception $e) {
            throw new Exception("Error Processing Request", $e);
        }
    }
}
