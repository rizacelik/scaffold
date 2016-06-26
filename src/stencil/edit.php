<?php
return <<<EOT
@extends('layouts.app')
@section('content')
<div class="container">
<h2>Edit <span class='muted'>${!${''} =  ucfirst(str_replace('_', ' ', $key))}</span></h2>
<hr>
@include('{$app_var}._form', ['$app_var' => \$$app_var, 'action'=> 'update'])
<p>
	<a href="{{url('{$app_var}/show/'.\${$app_var}->{$id})}}"> View</a> |
	<a href="{{url('$app_var')}}">Back</a>
</p>
</div>
@endsection
EOT;
?>
