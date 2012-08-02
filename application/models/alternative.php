<?php

class Alternative extends Eloquent {

	public static $timestamps = false;

	public function poll()
	{
		return $this->belongs_to('Poll');
	}
}