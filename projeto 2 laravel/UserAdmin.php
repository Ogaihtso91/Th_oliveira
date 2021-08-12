<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\AdminResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAdmin extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    /*********** PARAMETERS ***********/
    protected $table = 'users_admin';

    protected $guard_name = 'admin';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'objectguid',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $date = ['created_at'];

    /**
     * Send the password reset notification.
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }

    /** TODO - pode ser alterado */
    public function evaluations()
    {
        return $this->hasMany(SocialTecnologyEvaluatorStepEvaluation::class,'evaluator_id','id');
    }


    public function getIsFromLdap()
    {
        return $this->objectguid !== null;
    }
}
