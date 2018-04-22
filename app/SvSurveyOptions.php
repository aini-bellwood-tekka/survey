<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SvSurveyOptions extends Model
{
    protected $table = 'survey_options';
    protected $guarded = array('id');
    public $timestamps = true;
}
