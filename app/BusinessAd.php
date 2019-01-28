<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessAd extends Model
{
    use SoftDeletes;
	
    protected $table = "business_ads";

    protected $fillable = ['user_id', 'title', 'description', 'url'];

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function images() {
    	return $this->hasMany('App\BusinessAdImage');
    }
}
