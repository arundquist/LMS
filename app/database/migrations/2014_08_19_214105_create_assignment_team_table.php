<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssignmentTeamTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assignment_team', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('assignment_id')->unsigned()->index();
			$table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
			$table->integer('team_id')->unsigned()->index();
			//$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
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
		Schema::drop('assignment_team');
	}

}
