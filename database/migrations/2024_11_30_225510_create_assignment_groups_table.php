<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assignment_groups', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->unsignedDecimal('weight', 5, 2);
			$table->unsignedDecimal('lowest_degree', 5, 2);
			$table->unsignedDecimal('highest_degree', 5, 2);
			$table->boolean('status')->default(false);
			
			$table->timestamps(); // Created at and Updated at
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('assignment_groups');
	}
};