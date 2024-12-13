<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::create('grading_schemes', static function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->decimal('min_percentage', 5, 2);
			$table->decimal('max_percentage', 5, 2);
			$table->timestamps();
		});
	}
	
	public function down()
	{
		Schema::dropIfExists('grading_schemes');
	}
};