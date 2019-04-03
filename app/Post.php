<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $fillable = ['caption' , 'image' , 'type' , 'status' , 'user_id',
							'price'];


	public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function post_action()
    {
        return $this->hasMany('App\PostActions' , 'action_perform_user_id');
    }
    


}
