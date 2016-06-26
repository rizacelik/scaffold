<?php 
$content = "<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\\{$app_name};


class {$app_name}Controller extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 *
	 * Route::get('$app_var', '{$app_name}Controller@index')->name('$app_var.index');
	 */
	public function index()
	{
		\$$app_var = $app_name::all();

		return view('$app_var.index', compact('$app_var',\$$app_var));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 *
	 * Route::get('$app_var/create', '{$app_name}Controller@create')->name('$app_var.create');
	 */
	public function create()
	{
		return view('$app_var.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request \$request
	 * @return Response
	 *
	 * Route::post('$app_var/store', '{$app_name}Controller@store');
	 */
	public function store(Request \$request)
	{
	     \$this->validate(\$request, [

$rules
		 ]);
		 
		\$$app_var = new $app_name();

";

foreach ($fields as $field):
	$content .= "\t\t\${$app_var}->{$field} = \$request->input('$field');" . PHP_EOL;
endforeach;

$content .= "
		\${$app_var}->save();

		return redirect()->route('$app_var.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  \$$id
	 * @return Response
	 *
	 * Route::get('$app_var/show/{{$id}}', '{$app_name}Controller@show');
	 */
	public function show(\$$id)
	{
		\$$app_var = $app_name::findOrFail(\$$id);

		return view('$app_var.show', compact('$app_var',\$$app_var));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  \$$id
	 * @return Response
	 *
	 * Route::get('$app_var/edit/{{$id}}', '{$app_name}Controller@edit');
	 */
	public function edit(\$$id)
	{
		\$$app_var = $app_name::findOrFail(\$$id);

		return view('$app_var.edit', compact('$app_var',\$$app_var));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  \$$id
	 * @param Request \$request
	 * @return Response
	 *
	 * Route::put('$app_var/update/{{$id}}', '{$app_name}Controller@update');
	 */
	public function update(Request \$request, \$$id)
	{
		\$$app_var = $app_name::findOrFail(\$$id);

";

foreach ($fields as $field):
	$content .= "\t\t\${$app_var}->{$field} = \$request->input('$field');" . PHP_EOL;
endforeach;

$content .= "
		\${$app_var}->save();

		return redirect()->route('$app_var.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  \$$id
	 * @return Response
	 *
	 * Route::get('$app_var/delete/{{$id}}', '{$app_name}Controller@destroy');
	 */
	public function destroy(\$$id)
	{
		\$$app_var = $app_name::findOrFail(\$$id);
		\${$app_var}->delete();

		return redirect()->route('$app_var.index')->with('message', 'Item deleted successfully.');
	}

}
";
return $content;
?>
