<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * @return belongsToMany

    public function users()
    {
        return $this->belongsToMany('App\User', 'transaction_user', 'transaction_id', 'sender_id')
        			->withPivot('receiver_id')
        			->withTimestamps();
    }
    */
}
