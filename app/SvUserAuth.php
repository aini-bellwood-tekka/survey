<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SvUserAuth extends Model
{
    protected $table = 'user_auth';
    protected $guarded = array('id');
    public $timestamps = true;
}
