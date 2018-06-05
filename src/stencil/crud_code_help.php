<?php
$template ='';
$valid = 'public function store(Request $request){'. PHP_EOL . PHP_EOL;
$valid .= '$this->validate($request, ['. PHP_EOL;
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
        $template .= "\$$parent = {$parent}::find(\$request->input('id'));" . PHP_EOL . PHP_EOL;
        foreach ($value as $child => $val) {
            $lengths = array_map('strlen', $val);
            $lengths = max($lengths) + strlen("\${$child}->");
            $template .= "\$$child = new {$child}();". PHP_EOL;
            foreach ($val as $column) {
                $template .= str_pad("\${$child}->{$column}",$lengths)." = \$request->input('$column');" . PHP_EOL;
            }
            $template .= "\${$child}->save();". PHP_EOL;
            $template .= "\${$parent}->{$child}()->attach(\${$child}->id);". PHP_EOL . PHP_EOL;
        }
    }
    $template .= "return redirect()->route('".strtolower($child).".index')->with('message', 'Item created successfully.');". PHP_EOL;
    $template .= '}'. PHP_EOL . PHP_EOL;

} else {

    $template .= str_repeat('-----',15) . PHP_EOL;
    $template .= 'Relation: create method'. PHP_EOL;
    $template .= str_repeat('-----',15) . PHP_EOL . PHP_EOL;
    $template .= $valid;
    foreach ($merged as $parent => $value) {
        $template .= "\$$parent = $parent::find(\$request->input('id'));" . PHP_EOL;
        foreach ($value as $child => $val) {
            $lengths = array_map('strlen', $val);
            $lengths = max($lengths) + 2;
            $template .= "\$$child = \${$parent}->{$child}()->create([". PHP_EOL;
            foreach ($val as $column) {
                $template .= "\t".str_pad("'$column'",$lengths)." => \$request->input('$column')," . PHP_EOL;
            }
            $template .= "]);". PHP_EOL . PHP_EOL;
        }
    }
    $template .= "return redirect()->route('".strtolower($parent).".index')->with('message', 'Item created successfully.');". PHP_EOL;
    $template .= '}'. PHP_EOL . PHP_EOL;

    $template .= str_repeat('-----',15) . PHP_EOL;
    $template .= 'Relation: associate method'. PHP_EOL;
    $template .= str_repeat('-----',15) . PHP_EOL . PHP_EOL;
    $template .= $valid;
    foreach ($merged as $parent => $value) {
        $template .= "\$$parent = {$parent}::find(\$request->input('id'));" . PHP_EOL . PHP_EOL;
        foreach ($value as $child => $val) {
            $lengths = array_map('strlen', $val);
            $lengths = max($lengths) + strlen("\${$child}->");
            $template .= "\$$child = new {$child}();". PHP_EOL;
            foreach ($val as $column) {
                $template .= str_pad("\${$child}->{$column}",$lengths)." = \$request->input('$column');" . PHP_EOL;
            }
            $template .= PHP_EOL . "\${$child}->{$parent}()->associate(\$$parent);". PHP_EOL;
            $template .= "\${$child}->save();". PHP_EOL . PHP_EOL;
        }
    }
    $template .= "return redirect()->route('".strtolower($parent).".index')->with('message', 'Item created successfully.');". PHP_EOL;
    $template .= '}'. PHP_EOL . PHP_EOL;

    $template .= "// ------  Read one data ------ " . PHP_EOL;
    $template .= "\${$parent} = {$parent}::find(1);" . PHP_EOL;
    $template .= "echo \${$parent}->id;" . PHP_EOL;

    $template .= "foreach (\${$parent}->{$child} as \$data) {" . PHP_EOL;
    $template .= "   echo \$data->{$column};" . PHP_EOL;
    $template .= "}" . PHP_EOL . PHP_EOL ;

    $template .= "//------- Read all data -------- " . PHP_EOL;
    $template .= "\${$parent} = {$parent}::all();" . PHP_EOL;

    $template .= "foreach (\${$parent} as \$relate) {" . PHP_EOL;
    $template .= "    echo \$relate->id;" . PHP_EOL;
    $template .= "    foreach(\$relate->{$child} as \$data){" . PHP_EOL;
    $template .= "       echo \$data->{$column};" . PHP_EOL;
    $template .= "    }" . PHP_EOL;
    $template .= "}" . PHP_EOL . PHP_EOL;
}
return $template;
