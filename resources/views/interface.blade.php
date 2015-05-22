@extends('layout')

@section('styles')
<link href="{{ asset('/css/interface_style.css') }}" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Roboto:700' rel='stylesheet' type='text/css'>
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Steve Thinker</div>
				<div class="panel-body">
					<div id="response_cnt">
					</div>
					<form action method="post">
						<input type="text" id="text_input" class="form-control" autocomplete="off" />
						<input type="submit" id="submit_input" class="btn btn-success btn-lg" value="Speak" />
					</form>
					<h2>Language Key</h2>
					<div class="row">
						<div class="col-md-4">
							<div class="explain_bubble">
								<p><strong class="part_symbol"> . </strong>Nouns </p>
								<small>.Steve .Paris .water .people .time</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> * </strong>Adjectives </p>
								<small>*beautiful *funny *evil *fourtytwo *7</small>
							</div>
							<div class="explain_bubble">
								<strong class="part_symbol"> ` </strong><p>Articles </p>
								<small>`the `a `an</small>
							</div>
							<div class="explain_bubble">
								<strong class="part_symbol"> + </strong>or<strong> - </strong><p>Cheers and Jeers </p>
								<small>+yay -boo +maybe -maybe +probably</small>
							</div>
							<div class="explain_bubble">
								<strong class="part_symbol"> ++ </strong>or<strong> -- </strong><p>Positive and Negative </p>
								<small>++yes --no</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="explain_bubble">
								<p><strong class="part_symbol"> ! </strong>Do Verbs </p>
								<small>!do !does !did !act </small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> = </strong>Is Verbs </p>
								<small>=is =was =be =are </small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> > </strong>Go Verbs </p>
								<small> >go >come >went >bring </small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> ; </strong>Make Verbs </p>
								<small> ;make ;create ;cause ;bring </small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="explain_bubble">
								<p><strong class="part_symbol"> @ </strong>Prepositions of Time</p>
								<small>@at @on</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> # </strong>Prepositions of Space</p>
								<small>#at #on</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> $ </strong>Prepositions of Relation</p>
								<small>$of $with $like $who</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> ? </strong>Inquiry </p>
								<small>?why ?does ?will ?who</small>
							</div>
							<div class="explain_bubble">
								<p>Pronouns are not allowed </p>
								<small>this that him she</small>
							</div>
							<div class="explain_bubble">
								<p>Conjunctions are not allowed </p>
								<small>and but then</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/js') }}/interface_script.js"></script>
@endsection