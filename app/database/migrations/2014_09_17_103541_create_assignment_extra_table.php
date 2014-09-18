<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssignmentExtraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assignment_extra', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('assignment_id')->unsigned()->index();
			//$table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
			$table->integer('extra_id')->unsigned()->index();
			//$table->foreign('extra_id')->references('id')->on('extras')->onDelete('cascade');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('assignment_extra');
	}

}
