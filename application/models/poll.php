<?php

class Poll extends Eloquent {
	
	public function alternatives()
	{
		return $this->has_many('Alternative');
	}

}