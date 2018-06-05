<?php
return <<<EOT
@if (count(\$errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach (\$errors->all() as \$error)
                <li>{{ \$error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<?php
    \$method = \$action == 'update' ? '<input type="hidden" name="_method" value="PUT">' : '';
    \$action = \$action == 'update' ? \$action.'/'.\${$app_var}->{$id} : \$action;
?>
<form action="<?= url('$app_var/'.\$action) ?>" method="POST" class="form-horizontal">
<?= \$method ?>
    <fieldset>
$forms
        <div class="form-group">
            <label class="control-label">&nbsp;</label>
            <input type="submit" value="Save" class="btn btn-primary">
        </div>
    </fieldset>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>

EOT;
