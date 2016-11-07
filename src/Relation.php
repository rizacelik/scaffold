<?php

namespace Scaffold\Builder;

trait Relation
{
    
    public $belongsto = array();
    public $hasmany = array();
    
    public function relate()
    {
        
        $manycolumn = array();
	$belongsto  = array();
	$hasmany    = array();
        
        $query = \DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')->whereRaw('TABLE_SCHEMA = Database()')->whereRaw('REFERENCED_TABLE_NAME IS NOT NULL')->get();
        foreach ($query as $row) {
            $manycolumn[$row->TABLE_NAME][] = $row->COLUMN_NAME;
        }
        
        foreach ($query as $row) {
            
            $table     = ucfirst(camel_case($row->TABLE_NAME));
            $ref_table = ucfirst(camel_case($row->REFERENCED_TABLE_NAME));
            
            $code[str_replace('_', '', $row->TABLE_NAME)][$row->REFERENCED_TABLE_NAME] = $row->REFERENCED_TABLE_NAME;
            
            if (count($manycolumn[$row->TABLE_NAME]) < 2) {
                array_unshift($manycolumn[$row->TABLE_NAME], 'id');
            }
            $join = "'" . implode("', '", $manycolumn[$row->TABLE_NAME]) . "'";
            
            $hasmany[$row->REFERENCED_TABLE_NAME][$table] = array(
                "    public function $table() {
		return \$this->hasMany('App\\{$table}', '{$row->COLUMN_NAME}', '{$row->REFERENCED_COLUMN_NAME}');
    }" . PHP_EOL
            );
            
            $belongsto[$row->TABLE_NAME][$ref_table] = array(
                "    public function $ref_table() {
		return \$this->belongsToMany('App\\{$ref_table}', '{$row->TABLE_NAME}', {$join});
    }"
            );
        }
        
        $this->belongsto = $belongsto;
        $this->hasmany   = $hasmany;
        return count($manycolumn) > 0 ? $manycolumn : $this->noRelate();
    }
    
    public function noRelate()
    {
        
        $query = \DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')->whereRaw('TABLE_SCHEMA = Database()')->whereRaw('REFERENCED_TABLE_NAME IS NULL')->get();
        
        $normal = array();
        
        foreach ($query as $row) {
            $file       = ucfirst(camel_case($row->TABLE_NAME));
            $controller = app_path('Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $file . 'Controller.php');
            $model      = app_path($file . '.php');
            //if (!file_exists($controller) && !file_exists($model)) {
                $normal[$row->TABLE_NAME] = $row->TABLE_NAME;
            //}
        }
        return $normal;
    }
    
}
