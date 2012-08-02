@layout('templates.main')

@section('title')
	QuickPoll - create
@endsection

@section('content')
	<div class="hero-unit">
		<h1>{{ $question }}</h1><br />
		{{ Form::open('poll/create') }}
			<!-- check for errors -->
			@if (Session::has('errors'))
				@foreach($errors as $error)
					<span class="alert-error">{{ $error }}</span><br />
				@endforeach
			@endif

			<!-- hidden fields -->
			{{ Form::hidden('question', $question) }}
			{{ Form::hidden('alternative_count', $alternativeCount) }}

			<!-- alternatives -->
			@for ($i = 0; $i < $alternativeCount; $i++)
				<p>{{ Form::text('alt'.$i, '', array('placeholder' => 'Alternative '.($i + 1))) }}</p>
			@endfor

			<!-- create button -->
			<p>{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}</p>
		{{ Form::close() }}
	</div>
@endsection