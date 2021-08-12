<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    const EMAIL     = 'E';
    const TELEPHONE = 'T';
    const CELLPHONE = 'C';

    protected $fillable = ['type', 'value', 'branch_id', 'created_at'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public static function typeContacts()
    {
        return ['C' => 'Celular', 'E' => 'E-mail', 'T' => 'Telefone'];
    }
}
