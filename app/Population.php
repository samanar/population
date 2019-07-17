<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Population extends Model
{
    public $timestamps = false;
    protected $fillable = ['province', 'section_1', 'section_2', 'urban', 'rural', 'population'];
}
