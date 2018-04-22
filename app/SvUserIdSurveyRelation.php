<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SvUserIdSurveyRelation extends Model
{
    protected $table = 'user_id_survey_relation';
    protected $guarded = array('id');
    public $timestamps = true;
}
