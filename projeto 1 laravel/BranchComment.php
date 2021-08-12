<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchComment extends Model
{
    //
    protected $fillable = [
        'status', 'commentary', 'star', 'user_id', 'branch_id' 
    ];

    const APPROVED = 'A';
    const PENDING = 'P';
    const REFUSED = 'R';

    const DESCRIPTIONS = [
        'P' => 'Pendente',
        'R' => 'Reprovado',
        'A' => 'Aprovado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getCreatedAtFormatAttribute()
    {
        $x = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', is_null($this->attributes['created_at']) ? '1969-01-01 01:00:00' : $this->attributes['created_at'] )->format('d/m/Y');
        return $x;
    }


    public function getIsPendingAttribute() { return $this->attributes['status'] == self::PENDING; }
    public function getIsApprovedAttribute() { return $this->attributes['status'] == self::APPROVED; }
    public function getIsReprovedAttribute() { return $this->attributes['status'] == self::REFUSED; }

    public function getStatusDescAttribute()
    {
        return self::DESCRIPTIONS[$this->attributes['status']];
    }
}
