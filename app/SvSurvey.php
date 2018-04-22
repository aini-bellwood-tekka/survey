<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SvSurvey extends Model
{
    protected $table = 'survey';
    protected $guarded = array('id');
    public $timestamps = true;
}
