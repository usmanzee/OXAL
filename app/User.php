<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'cnic_or_passport_number', 'phone_number', 'avatar_name', 'avatar_name_without_ext', 'avatar_ext', 'verification_code' ,'verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function products() {
        return $this->hasMany('App\Product');
    }

    public function receivedReviews() {
        $this->hasMany('App\UserReview');
    }

    public function givenReviews() {
        $this->hasMany('App\UserReview', 'user_reviewer_id', 'id');
    }

    public function getUserById($id="") {

        return self::where('id', $id)->first();
    }

    public function getUserByPhoneNumber($phoneNumber="") {

        return self::where('phone_number', $phoneNumber)->first();
    }
}
