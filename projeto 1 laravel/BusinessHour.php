<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    private static $days = [
        'sun' => 'Domingo',
        'mon' => 'Segunda',
        'tue' => 'Terça',
        'wed' => 'Quarta',
        'thu' => 'Quinta',
        'fri' => 'Sexta',
        'sat' => 'Sabado'
    ];
    protected $fillable = ['branch_id', 'sun_24h', 'sun_closed', 'sun_open', 'sun_close', 'mon_24h', 'mon_closed', 'mon_open', 'mon_close', 'tue_24h', 'tue_closed', 'tue_open', 'tue_close', 'wed_24h', 'wed_closed', 'wed_open', 'wed_close', 'thu_24h', 'thu_closed', 'thu_open', 'thu_close', 'fri_24h', 'fri_closed', 'fri_open', 'fri_close', 'sat_24h', 'sat_closed', 'sat_open', 'sat_close'];

    protected $appends = ['today_status'];


    public function branch() 
    {
        return $this->belongsTo(Branch::class);
    }

    public static function days()
    {
        return array_keys(self::$days);
    }
    public function daysAsArray()
    {
        $data = $this->attributes;
        $dataAsArray = [ 'id' => $data['id'] ];
        foreach(self::days() as $day){
            foreach(['24h', 'open', 'close', 'closed'] as $time){
                $dataAsArray[$day][$time] = @$data["{$day}_{$time}"];
            }
        }
        return $dataAsArray;
    }

    public function getTodayStatusAttribute()
    {
        $today = strtolower((new \DateTime())->format('D'));
        $return = [
            '24h' => $this->{$today . '_24h'},
            'closed' => $this->{$today . '_closed'},
            'open' => $this->{$today . '_open'},
            'close' => $this->{$today . '_close'},
        ];
        return self::status($return, true);
    }

    public static function dayName($day)
    {
        return isset(self::$days[$day]) ? self::$days[$day] : '';
    }

    public static function status($info, $array = false)
    {
        if(!$array) {
            if($info['24h'] == 'Y')
                return 'Aberto 24 horas';
            
            if($info['closed'] == 'Y')
                return 'Fechado';

            if(!empty($info['open']) && !empty($info['close']))
                return "{$info['open']} - {$info['close']}";
        } else {
            return [
                '24h' => $info['24h'] == 'Y',
                'closed' => $info['closed'] == 'Y',
                'open' => $info['open'],
                'close' => $info['close'],
            ];
        }
    }
}
