<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Award;
use App\Enums\AutoMessageTypes;

class AutoMessage extends Model
{

    protected $fillable = [
        'type',
        'message'
    ];

    protected $casts = [
        'type' => 'integer',
    ];

    public function typeDescription() {
        return AutoMessageTypes::getDescription( $this->type )  ;
    }

    public function subjectMessage() {
        return $this->typeDescription() . ' – ' . $this->award->name ;
    }

    public function award() {
        return $this->belongsTo(Award::class);
    }

    /**
     * save a message into db;
     * @param Array $data
     * @return $this AutoMessage instance
     */
    public function store(array $data) {
        // search for the new award
        $new_award = Award::find($data['award']);

        if(empty($data['id'])) {

            // if it's a new msg
            // unset award data
            unset($data['award']);

            // create a new msg instace
            $msg_obj = new AutoMessage([
                'type' => $data['type'],
                'message' => $data['message_body']
            ]);

            // then associate to a new award
            $msg_obj = $new_award->messages()->save($msg_obj);

        } else {
            // it's a old msg
            $msg_obj = self::find($data['id']);
            unset($data['id']);

            // verify if award is the same
            if($data['award'] != $msg_obj->award_id) {
                // award is not the same so
                // dissociate old award
                $msg_obj->award()->dissociate($msg_obj->award);

                //associate new the one
                $msg_obj->award()->associate($new_award);
            }
            // unset award field
            unset($data['award']);
            // update
            $msg_obj->update([
                'type' => $data['type'],
                'message' => $data['message_body']
            ]);
        }

        return $msg_obj;
    }
}
