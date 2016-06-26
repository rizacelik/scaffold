<?php
$content = <<<EOT
@extends('layouts.app')
@section('content')
<div class="container">
<h2>Viewing <span class='muted'><?=\${$app_var}->{$id}?></span></h2>
<br>
EOT;

foreach ($fields as $field):
		$content .= "\t" . '<p><strong>' . ucwords(str_replace('_', ' ', $field)) . '</strong>' . PHP_EOL;
		$content .= "\t<?=\$" . $app_var . '->' . $field . '?><p>' . PHP_EOL;
endforeach;

$content .= <<<EOT
<p>
	<a href="{{url('$app_var/edit/'.\${$app_var}->{$id})}}"> Edit</a> |
	<a href="{{url('$app_var')}}">Back</a>
</p>
</div>
@endsection
EOT;
return $content;
?>
