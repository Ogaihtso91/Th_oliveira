<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'service_provider_id', 'number_of_stars', 'name', 'address_id', 'address_number', 'cep', 'lat', 'lng', 'visible', 
    ];

    protected $appends = ['slug'];

    protected $guarded = [
        'id', 'created_at', 'update_at'
    ];

    /* Related */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function businessHour()
    {
        return $this->hasOne(BusinessHour::class);
    }

    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    public function contacts($specific = false)
    {
        if(!$specific){
            return $this->hasMany(Contact::class);
        } else {
            return $this->hasMany(Contact::class)->where('type', $specific);
        }
    }

    public function rating()
    {
        return $this
            ->hasMany(BranchComment::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function galleries()
    {
        return $this->hasMany(BranchGallery::class);
    }

    public function activeComments()
    {
        return $this
            ->hasMany(BranchComment::class)
            ->where('status', BranchComment::APPROVED)
            ->whereNotNull('commentary')
            ->orderBy('id','desc');
    }

    public function telephones()    { return $this->contacts(Contact::TELEPHONE); }
    public function emails()        { return $this->contacts(Contact::EMAIL); }
    public function cellphones()    { return $this->contacts(Contact::CELLPHONE); }

    /* Attribute Mutators */
    public function getSlugAttribute() 
    {
        return str_slug("{$this->serviceProvider->name} {$this->name} {$this->id}");
    }

    public function getAddressPathAttribute()
    {
        return "{$this->address->type} {$this->address->name}, {$this->address->district->name}";
    }

    public function getVisibleAttribute()
    {
        return empty($this->attributes['visible']) ? 'Y' : $this->attributes['visible'];
    }


    public function getIsVisibleAttribute()
    {
        return $this->attributes['visible'] == 'Y';
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address->type . ' ' . $this->address->name;
        $number = $this->address_number;
        $district = $this->address->district->name;
        $city = $this->address->district->city->name;
        $state = $this->address->district->city->state->abbr;
        $cep = $this->cep;;

        $j = array_filter([
            $address, $number, $district, $city, $state, $cep
        ]);
        return join(', ', $j);
    }


    


}
