<?php
$content = <<<EOT
@extends('layouts.app')
@section('content')
<div class="container">
<h2>Listing <span class='muted'>${!${''} = ucfirst(str_replace('_', ' ', $key))}</span></h2>
<a href="<?=url('$app_var/create')?>" class = "btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Create $app_var</a>
<hr>
@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
<?php if (isset(\$$app_var)): ?>

<table class="table table-striped">
	<thead>
		<tr>

EOT;

foreach ($fields as $field):
	$content .= "\t\t\t" . '<th>' . ucwords(str_replace('_', ' ', $field)) . '</th>' . PHP_EOL;
endforeach;

$content .= '			<th width="20%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($' . $app_var . ' as $item): ?>
		<tr>
';

foreach ($fields as $field):
	$content .= "\t\t\t" . '<td><?= $item->' . $field . ' ?></td>' . PHP_EOL;
endforeach;

$content .= <<<EOT
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<a href="<?=url('$app_var/show/'.\$item->{$id})?>" class = "btn btn-info btn-sm"><i class="glyphicon glyphicon-eye-open"></i> View</a>
						<a href="<?=url('$app_var/edit/'.\$item->{$id})?>" class = "btn btn-warning btn-sm"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
						<a href="<?=url('$app_var/delete/'.\$item->{$id})?>" class = "btn btn-danger btn-sm" onclick = "return confirm('Are you sure?')"><i class="glyphicon glyphicon-trash"></i> Delete</a>
					</div>
				</div>

			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>

<p>No $app_var . </p>

<?php endif; ?>

</div>
@endsection
EOT;
return $content;
?>
