<?php
return <<<EOT
@extends('layouts.app')
@section('content')
<div class="container">
<h2>Create <span class='muted'>${!${''} = ucfirst(str_replace('_', ' ', $key))}</span></h2>
<hr>

@include('{$app_var}._form', ['action'=> 'store'])

<p><a href="{{url('$app_var')}}">Back</a></p>
</div>
@endsection
EOT;
