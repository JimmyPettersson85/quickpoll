<?php

class Poll_Controller extends Base_Controller {

	public $restful = true;

	public function get_index()
	{
		return View::make('poll.index');
	}

	public function post_index()
	{
		$input = array(
			'question' => Input::get('question'),
			'alternativeCount' => Input::get('alternative_count')
		);

		$rules = array(
			'question' => 'required|min:3',
			'alternativeCount' => 'required|integer|min:2'
		);

		$v = Validator::make($input, $rules);
		if ($v->fails())
		{
			return Redirect::to_action('poll@index')
				->with_input()
				->with('errors', $v->errors->all());
		}
		else
		{
			return View::make('poll.create', $input);
		}
	}

	public function get_create()
	{
		return Redirect::to_action('poll@index');
	}

	public function post_create()
	{
		$input = array(
			'question' => Input::get('question'),
			'alternativeCount' => (int) Input::get('alternative_count')
		);

		$rules = array(
			'question' => 'required|min:3',
			'alternativeCount' => 'required|integer|min:2'
		);

		$v = Validator::make($input, $rules);
		if ($v->fails())
		{
			return Redirect::to_action('poll@index')
				->with_input()
				->with('errors', $v->errors->all());
		}
		else
		{
			$alternatives = Input::except(array('question', 'alternative_count'));
			$alternatives = $this->filterAlternatives($alternatives);
			if (count($alternatives) < 2)
			{
				return Redirect::to_action('poll@index')
					->with('errors', array('Too few alternatives'));
			}
			else
			{
				$id = $this->createPoll($input['question'], $alternatives);
				if ($id)
				{
					return Redirect::to('poll/view/'.$id);
				}
				else
				{
					return Redirect::to_action('poll@index')
						->with('errors', array('Error creating poll'));
				}
			}
		}
	}

	public function get_view($id=0)
	{
		if (! $id)
		{
			return Redirect::to_action('poll@index')
				->with('errors', array('No poll found, create one?'));			
		}
		$poll = Poll::find($id);
		if (! $poll)
		{
			return Redirect::to_action('poll@index')
				->with('errors', array('No poll found, create one?'));
		}

		$count = Alternative::where('poll_id' , '=', $poll->id)->sum('votes');

		return View::make('poll.view', array('poll' => $poll, 'count' => $count));
	}

	public function post_view()
	{
		$id = Input::get('alternative');
		if ($id)
		{
			$alternative = Alternative::find($id);
			$alternative->votes += 1;
			$alternative->save();
			return Redirect::to('poll/view/'.$alternative->poll->id);
		}
		else
		{
			return Redirect::to('poll/view/'.Input::get('poll_id'))
				->with('errors', array('Invalid alternative'));
		}
	}

	private function createPoll($question, $alternatives)
	{
		$poll = new Poll();
		$poll->question = $question;
		if (! $poll->save()) return false;

		foreach ($alternatives as $alternative) {
			$alt = new Alternative();
			$alt->alternative = $alternative;
			$alt->poll_id = $poll->id;
			if (! $alt->save()) return false;
		}

		return $poll->id;
	}

	private function filterAlternatives($alternatives)
	{
		$filteredAlternatives = array();
		foreach ($alternatives as $key => $value) {
			if (! strlen(trim($value))) continue;
			$filteredAlternatives[] = $value;
		}

		return $filteredAlternatives;
	}
}