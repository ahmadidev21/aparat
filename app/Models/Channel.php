<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'channels';

    protected $fillable = [
        'user_id',
        'name',
        'info',
        'banner',
        'socials',
    ];

    protected $casts=[
        'socials'=>'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //region override method
    public function getRouteKeyName()
    {
        return 'name';
    }
    //endregion override method

    /**
     * we replace $cats['socials']='json' with bellow code
     */
//    public function setSocialsAttribute($value)
//    {
//        if(is_array($value)){
//            $value = json_encode($value);
//            $this->attributes['socials'] = $value;
//        }
//    }
//
//    public function getSocialsAttribute()
//    {
//        return json_decode($this->attributes['socials'], true);
//    }
}
