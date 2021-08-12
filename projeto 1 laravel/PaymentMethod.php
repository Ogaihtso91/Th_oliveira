<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    private $id;
    private $name;

    public function getIconAttribute()
    {
        $id = $this->attributes['id'];
        return "/assets/img/payment-methods/{$id}.png";
    }
}
