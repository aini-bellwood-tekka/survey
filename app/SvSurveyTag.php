<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SvSurveyTag extends Model
{
    protected $table = 'survey_tag';
    protected $guarded = array('id');
    public $timestamps = true;
    public function survey()
    {
        return $this->hasOne('App\SvSurvey','id', 'survey_id');
    }
}
