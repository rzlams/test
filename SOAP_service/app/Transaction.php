<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * @return belongsTo
     */
    public function users()
    {
        return $this->belongsTo('App\User');
    }
}
