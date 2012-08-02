<?php

class Create_Alternatives {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alternatives', function($table) {
			$table->increments('id');
			$table->integer('poll_id');
			$table->foreign('poll_id')->references('id')->on('polls');
			$table->string('alternative', 256);
			$table->integer('votes');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alternatives');
	}

}