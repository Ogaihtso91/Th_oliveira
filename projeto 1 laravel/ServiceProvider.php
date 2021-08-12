<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    
    const STATUS_ACTIVE     = 'Y';
    const STATUS_INACTIVE   = 'N';

    protected $fillable = [
        'name', 'cnpj', 'type_provider_id', 'user_responsible_id', 'active'
    ];

    protected $guarded = [
        'id', 'created_at', 'update_at'
    ];

    public function typeProvider()
    {
        return $this->belongsTo(TypeProvider::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'service_provider_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'user_responsible_id');
    }

    public function getIsActiveAttribute()
    {
        return $this->attributes['active'] == self::STATUS_ACTIVE;
    }


}
