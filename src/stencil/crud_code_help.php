<?php
$template ='';
$valid = 'public function store(Request $request){'. PHP_EOL . PHP_EOL;
$valid .= '$this->validate($request, ['. PHP_EOL;
$_parent = '';
$_child = '';
$_column = '';

foreach($required as $rule){
    $valid .= "\t" . $rule . PHP_EOL;
}
$valid .= "]);" . PHP_EOL . PHP_EOL;

if ($many) {

    $template = str_repeat('-----',15) . PHP_EOL;
    $template .= 'Relation: attach method'. PHP_EOL;
    $template .= str_repeat('-----',15) . PHP_EOL . PHP_EOL;
    $template .= $valid;
    foreach ($merged as $parent => $value) {
        $_parent = $parent;
        $template .= "\$$parent = {$parent}::find(\$request->input('id'));" . PHP_EOL . PHP_EOL;
        foreach ($value as $child => $val) {
            $_child = $child;
            $lengths = array_map('strlen', $val);
            $lengths = max($lengths) + strlen("\${$child}->");
            $template .= "\$$child = new {$child}();". PHP_EOL;
            foreach ($val as $column) {
                $_column = $column;
                $template .= str_pad("\${$child}->{$column}",$lengths)." = \$request->input('$column');" . PHP_EOL;
            }
            $template .= "\${$child}->save();". PHP_EOL;
            $template .= "\${$parent}->{$child}()->attach(\${$child}->id);". PHP_EOL . PHP_EOL;
        }
    }
    $template .= "return redirect()->route('".strtolower($_child).".index')->with('message', 'Item created successfully.');". PHP_EOL;
    $template .= '}'. PHP_EOL . PHP_EOL;

} else {

    $template .= str_repeat('-----',15) . PHP_EOL;
    $template .= 'Relation: create method'. PHP_EOL;
    $template .= str_repeat('-----',15) . PHP_EOL . PHP_EOL;
    $template .= $valid;
    foreach ($merged as $parent => $value) {
        $_parent = $parent;
        $template .= "\$$parent = $parent::find(\$request->input('id'));" . PHP_EOL;
        foreach ($value as $child => $val) {
            $_child = $child;
            $lengths = array_map('strlen', $val);
            $lengths = max($lengths) + 2;
            $template .= "\$$child = \${$parent}->{$child}()->create([". PHP_EOL;
            foreach ($val as $column) {
                $_column = $column;
                $template .= "\t".str_pad("'$column'",$lengths)." => \$request->input('$column')," . PHP_EOL;
            }
            $template .= "]);". PHP_EOL . PHP_EOL;
        }
    }
    $template .= "return redirect()->route('".strtolower($_parent).".index')->with('message', 'Item created successfully.');". PHP_EOL;
    $template .= '}'. PHP_EOL . PHP_EOL;

    $template .= str_repeat('-----',15) . PHP_EOL;
    $template .= 'Relation: associate method'. PHP_EOL;
    $template .= str_repeat('-----',15) . PHP_EOL . PHP_EOL;
    $template .= $valid;
    foreach ($merged as $parent => $value) {
        $_parent = $parent;
        $template .= "\$$parent = {$parent}::find(\$request->input('id'));" . PHP_EOL . PHP_EOL;
        foreach ($value as $child => $val) {
            $_child = $child;
            $lengths = array_map('strlen', $val);
            $lengths = max($lengths) + strlen("\${$child}->");
            $template .= "\$$child = new {$child}();". PHP_EOL;
            foreach ($val as $column) {
                $_column = $column;
                $template .= str_pad("\${$child}->{$column}",$lengths)." = \$request->input('$column');" . PHP_EOL;
            }
            $template .= PHP_EOL . "\${$child}->{$parent}()->associate(\$$parent);". PHP_EOL;
            $template .= "\${$child}->save();". PHP_EOL . PHP_EOL;
        }
    }
    $template .= "return redirect()->route('".strtolower($_parent).".index')->with('message', 'Item created successfully.');". PHP_EOL;
    $template .= '}'. PHP_EOL . PHP_EOL;

    $template .= "// ------  Read one data ------ " . PHP_EOL;
    $template .= "\${$_parent} = {$_parent}::find(1);" . PHP_EOL;
    $template .= "echo \${$_parent}->id;" . PHP_EOL;

    $template .= "foreach (\${$_parent}->{$_child} as \$data) {" . PHP_EOL;
    $template .= "   echo \$data->{$_column};" . PHP_EOL;
    $template .= "}" . PHP_EOL . PHP_EOL ;

    $template .= "//------- Read all data -------- " . PHP_EOL;
    $template .= "\${$_parent} = {$_parent}::all();" . PHP_EOL;

    $template .= "foreach (\${$_parent} as \$relate) {" . PHP_EOL;
    $template .= "    echo \$relate->id;" . PHP_EOL;
    $template .= "    foreach(\$relate->{$_child} as \$data){" . PHP_EOL;
    $template .= "       echo \$data->{$_column};" . PHP_EOL;
    $template .= "    }" . PHP_EOL;
    $template .= "}" . PHP_EOL . PHP_EOL;
}
return $template;
