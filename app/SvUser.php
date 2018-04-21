<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SvUser extends Model
{
    protected $table = 'user';
    protected $guarded = array('id');
    public $timestamps = true;
}
