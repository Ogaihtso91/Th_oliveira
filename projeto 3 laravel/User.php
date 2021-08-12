<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'foto', 'telefone_principal', 'cargo', 'telefone_secundario', 'cargo_FBB', 'telefone_comercial', 'acesso'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function reuniao(){

        return $this->belongsToMany(Reuniao::class, 'reunioes_user')
            ->withPivot('participacao', 'tipo_participacao');

    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function humanograma()
    {
        return $this->hasOne(Humanograma::class, 'usuario_id', 'id');
    }

    public function mandato()
    {
        return $this->hasOne(Mandato::class);
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtolower($value);
    }

    public function possuiCargoFBB($key)
    {
        $cargos = Collection::wrap(explode(',', $this->cargo_FBB));

        return $cargos->contains($key);
    }
}
