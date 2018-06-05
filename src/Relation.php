<?php

namespace Scaffold\Builder;

trait Relation
{
    public $table_name = array();
    public $template ='';

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
                $this->crud($row->REFERENCED_TABLE_NAME, $ref_table,true);
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
        $this->crud($row->TABLE_NAME, $ref_table);
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

    public function crud($table, $relate_table, $many = false)
    {
        $query    = \DB::table('INFORMATION_SCHEMA.COLUMNS')->whereRaw('TABLE_SCHEMA = Database()')->where('TABLE_NAME', $table)->get();
        $merged   = array();
        $required = array();
        foreach ($query as $info) {
            $columnName = $info->COLUMN_NAME;
            if ($info->COLUMN_KEY != 'PRI' && $columnName != 'created_at' && $columnName != 'updated_at') {
                if ($info->IS_NULLABLE == 'NO' && is_null($info->COLUMN_DEFAULT)) {
                    if ($info->COLUMN_KEY == 'MUL' && $many) {
                        $columnName = $info->COLUMN_NAME;
                        $type       = $info->DATA_TYPE == 'int' || $info->DATA_TYPE == 'tinyint' || $info->DATA_TYPE == 'smallint' ? '|numeric' : '';
                        $max        = !is_null($info->CHARACTER_MAXIMUM_LENGTH) ? '|max:' . $info->CHARACTER_MAXIMUM_LENGTH : '';
                        $uni        = $info->COLUMN_KEY == 'UNI' ? '|unique:' . $table : '';
                        $required[] = "'$columnName' => 'required$uni$type$max',";
                        $t_name     = ucfirst(camel_case($table));
                        $merged[$relate_table][$t_name][] = $columnName;
                    } elseif ($info->COLUMN_KEY != 'MUL') {
                        $columnName = $info->COLUMN_NAME;
                        $type       = $info->DATA_TYPE == 'int' || $info->DATA_TYPE == 'tinyint' || $info->DATA_TYPE == 'smallint' ? '|numeric' : '';
                        $max        = !is_null($info->CHARACTER_MAXIMUM_LENGTH) ? '|max:' . $info->CHARACTER_MAXIMUM_LENGTH : '';
                        $uni        = $info->COLUMN_KEY == 'UNI' ? '|unique:' . $table : '';
                        $required[] = "'$columnName' => 'required$uni$type$max',";
                        $t_name     = ucfirst(camel_case($table));
                        $merged[$relate_table][$t_name][] = $columnName;
                    }

                }
            }
        }

        $this->template .= include(__DIR__ . DIRECTORY_SEPARATOR . 'stencil' . DIRECTORY_SEPARATOR . 'crud_code_help.php');
    }
}
