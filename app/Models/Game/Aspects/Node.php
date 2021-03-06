<?php

namespace App\Models\Game\Aspects;

use App\Models\Game\Aspect;
use App\Models\Game\Aspects\Item;
use App\Models\Game\Aspects\Zone;
use App\Models\Game\Concepts\Detail;
use App\Models\Game\Concepts\Scroll;
use App\Models\Translations\NodeTranslation;

class Node extends Aspect
{

	public $translationModel = NodeTranslation::class;
	public $translatedAttributes = [ 'name' ];

	/**
	 * Relationships
	 */

	public function details()
	{
		return $this->morphOne(Detail::class, 'detailable');
	}

	public function zones()
	{
		return $this->morphToMany(Zone::class, 'coordinate')->withTranslation()->withPivot('x', 'y', 'z', 'radius');
	}

	public function scrolls()
	{
		return $this->morphToMany(Scroll::class, 'jotting')->withTranslation()->withPivot('quantity');
	}

	public function items()
	{
		return $this->belongsToMany(Item::class)->withTranslation();
	}

}
