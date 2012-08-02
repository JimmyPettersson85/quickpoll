<?php

/**
 * Acts as the index page for the application.
 *
 * @return View
 */
Route::get('poll/new', function ()
{
	return View::make('poll.new');
});

/**
 * Takes the user submitted data and if valid sends the
 * user to the next step in the creation of the poll.
 *
 * @return mixed
 */
Route::post('poll/new', function ()
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
		return Redirect::to('poll/new')
			->with_input()
			->with('errors', $v->errors->all());
	}
	else
	{
		return View::make('poll.create', $input);
	}
});

/**
 * Fetches the poll with id $id from the database
 * and displays it to the user.
 *
 * @param int $id
 * @return mixed
 */
Route::get('poll/(:num)', function($id)
{
	$poll = Poll::find($id);
	if (! $poll)
	{
		return Redirect::to('poll/new')
			->with('errors', array('No poll found, create one?'));
	}

	$count = Alternative::where('poll_id' , '=', $poll->id)->sum('votes');

	return View::make('poll.view', array('poll' => $poll, 'count' => $count));
});

/**
 * Regiesters a vote from the user to the current poll.
 *
 * @param int $id
 * @return Redirect
 */
Route::post('poll/(:num)', function($id)
{
	$alternativeId = Input::get('alternative');
	if ($alternativeId)
	{
		$alternative = Alternative::find($alternativeId);
		$alternative->votes += 1;
		$alternative->save();
		return Redirect::to('poll/'.$id);
	}
	else
	{
		return Redirect::to('poll/'.$id)
			->with('errors', array('Invalid alternative'));
	}
});

/**
 * Second step in the poll creation. Checks for any errors and
 * if none is found the poll is created in the database and the 
 * user is redireceted to the view page of the poll.
 *
 * @return Redirect
 */
Route::post('poll/create', function ()
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
		return Redirect::to_action('poll/new')
			->with_input()
			->with('errors', $v->errors->all());
	}
	else
	{
		$alternatives = Input::except(array('question', 'alternative_count'));
		$alternatives = PollHelper::filterAlternatives($alternatives);
		if (count($alternatives) < 2)
		{
			return Redirect::to('poll/new')
				->with('errors', array('Too few alternatives'));
		}
		else
		{
			$id = PollHelper::createPoll($input['question'], $alternatives);
			if ($id)
			{
				return Redirect::to('poll/'.$id);
			}
			else
			{
				return Redirect::to_action('poll/new')
					->with('errors', array('Error creating poll'));
			}
		}
	}
});

/**
 * Redirects roots to the 'new' route. 
 *
 * @return Recirect
 */
Route::get(array('/', 'poll'), function()
{
	return Redirect::to('poll/new');
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});