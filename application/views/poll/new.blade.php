@layout('templates.main')

@section('title')
	QuickPoll
@endsection

@section('content')
	<div class="hero-unit">
		{{ Form::open() }}
			<!-- check for errors -->
			@if (Session::has('errors'))
				@foreach($errors as $error)
					<span class="alert-error">{{ $error }}</span><br />
				@endforeach
			@endif

			<!-- question field -->
			<p>{{ Form::label('question', 'Poll Question') }}</p>
			<p>{{ Form::text('question', Input::old('question'), array('autocomplete' => 'off')) }}</p>

			<!-- number alternatives -->
			<p>{{ Form::label('alternative_count', 'Number of alternatives') }}</p>
			<p>{{ Form::number('alternative_count', 2) }}</p>

			<!-- create button -->
			<p>{{ Form::submit('Next', array('class' => 'btn btn-primary')) }}</p>
		{{ Form::close() }}
	</div>
@endsection