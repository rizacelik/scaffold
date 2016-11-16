<?php

namespace Scaffold\Builder;

trait Relation
{
    public $table_name = array();
    
    public function relate()
    {
        
        $manytables = array();
        $manycolumn = array();
        $belongsto  = array();
        $hasmany    = array();
        $manymany   = array();
        $column     = array();
        
        $query = \DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')->whereRaw('TABLE_SCHEMA = Database()')->whereRaw('REFERENCED_TABLE_NAME IS NOT NULL')->get();
        
        foreach ($query as $row) {
            $manytables[] = $row->TABLE_NAME;
            $manycolumn[$row->TABLE_NAME][] = $row->COLUMN_NAME;
            $manymany[$row->TABLE_NAME][]   = $row->REFERENCED_TABLE_NAME;
            $column[$row->TABLE_NAME][$row->REFERENCED_TABLE_NAME] = $row->COLUMN_NAME;
        }
        
        $count = array_count_values($manytables);
        
        foreach ($query as $row) {
            $table     = ucfirst(camel_case($row->TABLE_NAME));
            $ref_table = ucfirst(camel_case($row->REFERENCED_TABLE_NAME));
            $this->table_name[] = $row->REFERENCED_TABLE_NAME;
            $this->table_name[] = $row->TABLE_NAME;
            
            if ($count[$row->TABLE_NAME] > 1) {
                $find_table = array_pop($manymany[$row->TABLE_NAME]);
                $ref_table  = ucfirst(camel_case($find_table));
                $merge      = array(
                    $column[$row->TABLE_NAME][$row->REFERENCED_TABLE_NAME],
                    $column[$row->TABLE_NAME][$find_table]
                );
                $joincolumn = "'" . implode("', '", $merge) . "'";
                
                $belongsto[$row->REFERENCED_TABLE_NAME][$ref_table] = array(
                    "    public function $ref_table() {
		return \$this->belongsToMany('App\\{$ref_table}', '{$row->TABLE_NAME}', {$joincolumn});
    }" . PHP_EOL
                );
                
            } else {
                $hasmany[$row->REFERENCED_TABLE_NAME][$table] = array(
                    "    public function $table() {
		return \$this->hasMany('App\\{$table}', '{$row->COLUMN_NAME}', '{$row->REFERENCED_COLUMN_NAME}');
    }" . PHP_EOL
                );
                
                $belongsto[$row->TABLE_NAME][$ref_table] = array(
                    "    public function $ref_table() {
		return \$this->belongsTo('App\\{$ref_table}', '{$row->COLUMN_NAME}', '{$row->REFERENCED_COLUMN_NAME}');
    }" . PHP_EOL
                );
            }
        }
        
        return $this->array_merge_recursive_distinct($belongsto, $hasmany);
    }
    
    public function noRelate()
    {
        
        $query = \DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')->whereRaw('TABLE_SCHEMA = Database()')->whereRaw('REFERENCED_TABLE_NAME IS NULL')->get();
        
        $normal = array();
        
        foreach ($query as $row) {
            if (!in_array($row->TABLE_NAME, $this->table_name) && 'migrations' != $row->TABLE_NAME) {
                $normal[$row->TABLE_NAME] = $row->TABLE_NAME;
            }
        }
        return $normal;
    }
    
    public function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;
        
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        
        return $merged;
    }
}
