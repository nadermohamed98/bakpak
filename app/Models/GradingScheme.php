<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingScheme extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'name',
		'min_percentage',
		'max_percentage'
	];
}