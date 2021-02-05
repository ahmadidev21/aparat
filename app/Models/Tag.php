<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = ['title'];

    //region override model method
    public function toArray()
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title
        ];
    }
    //endregion override model method
}
