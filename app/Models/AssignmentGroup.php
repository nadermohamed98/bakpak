<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentGroup extends Model
{
	use HasFactory;
	
	protected $fillable = [
		'name',
		'weight',
		'lowest_degree',
		'highest_degree',
		'status',
	];
}