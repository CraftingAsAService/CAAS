<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class ScrollTranslation extends Model
{

	public $timestamps = false;

	protected $fillable = [ 'name', 'description' ];

}
