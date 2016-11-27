<?php

namespace Scaffold\Builder;

trait CreateBuilder
{
    
    protected $ds = DIRECTORY_SEPARATOR;
    protected $variable = array();
    protected $route_content = '';
    public $crud_code = '';
    
    public function _create($relation, $relate, $backup, $has = true)
    {
        $i = 0;
        foreach ($relation as $key => $table) {
            $rules      = '';
            $forms      = '';
            $properties = array();
            $fields     = array();
            $app_var    = strtolower(camel_case($key));
            
            $query = \DB::table('INFORMATION_SCHEMA.COLUMNS')->whereRaw('TABLE_SCHEMA = Database()')->where('TABLE_NAME', $key)->get();
            
            foreach ($query as $info) {
                $columnName = $info->COLUMN_NAME;
                if ($info->COLUMN_KEY != 'PRI' && $columnName != 'created_at' && $columnName != 'updated_at') {
                    $rule = array();
                    $forms .= "\t\t<div class=\"form-group\"><label class=\"control-label\">" . ucwords(str_replace('_', ' ', $columnName)) . "</label>" . PHP_EOL;
                    
                    $required = '';
                    $class    = 'class="col-md-4 form-control"';
                    $place    = 'placeholder="' . ucwords(str_replace('_', ' ', $columnName)) . '"';
                    
                    if ($info->IS_NULLABLE == 'NO') {
                        $rule[]   = 'required';
                        $required = "required ='required'";
                    }
                    if (in_array($info->DATA_TYPE, array('varchar', 'string', 'char'))) {
                        if ($columnName == 'email' || $columnName == 'mail') {
                            $rule[] = 'email';
                            $forms .= "\t\t<input type=\"email\" name=\"{$columnName}\" value =\"<?= isset(\${$app_var}) ? \${$app_var}->{$columnName} : ''?>\" $required $class $place></div>" . PHP_EOL;
                        } else {
                            $forms .= "\t\t<input type=\"text\" name=\"{$columnName}\" value =\"<?= isset(\${$app_var}) ? \${$app_var}->{$columnName} : ''?>\" $required $class $place></div>" . PHP_EOL;
                        }
                        $rule[] = !is_null($info->CHARACTER_MAXIMUM_LENGTH) ? "max:{$info->CHARACTER_MAXIMUM_LENGTH}" : 'max:255';
                    } elseif (in_array($info->DATA_TYPE, array('int', 'integer', 'tinyint'))) {
                        $rule[] = 'numeric';
                        $forms .= "\t\t<input type=\"number\" name=\"{$columnName}\" value =\"<?= isset(\${$app_var}) ? \${$app_var}->{$columnName} : ''?>\" $required $class $place></div>" . PHP_EOL;
                        
                    } elseif (in_array($info->DATA_TYPE, array('datetime', 'date', 'timestamp'))) {
                        $rule[] = 'date';
                        $forms .= "\t\t<input type=\"datetime\" name=\"{$columnName}\" value =\"<?= isset(\${$app_var}) ? \${$app_var}->{$columnName} : ''?>\" $required $class $place></div>" . PHP_EOL;
                        
                    } elseif ($info->DATA_TYPE == 'text') {
                        $forms .= "\t\t<textarea name=\"{$columnName}\" rows=10 cols=45 $class $required><?= isset(\${$app_var}) ? \${$app_var}->{$columnName} : '' ?></textarea></div>" . PHP_EOL;
                    } else {
                        $forms .= "\t\t<input type=\"datetime\" name=\"{$columnName}\" value =\"<?= isset(\${$app_var}) ? \${$app_var}->{$columnName} : ''?>\" $required $class $place></div>" . PHP_EOL;
                    }
                    
                    if (count($rule) > 0) {
                        $add = implode('|', $rule);
                        $rules .= str_repeat(' ', 12) . "'{$columnName}' => '{$add}'," . PHP_EOL;
                    }
                    $fields[] = $columnName;
                }
                $properties[] = $columnName;
            }
            
            $app_name = ucfirst(camel_case($key));
            
            $id = $properties[0];
            
            $fillable = "'" . implode("', '", $fields) . "'";
            
            $tablename = "protected \$table = '{$key}';";
            
            if ($relate == false) {
                $imp = '';
            } else {
                $imp = PHP_EOL;
                foreach ($table as $values) {                    
                    $imp .= $values[0] . PHP_EOL;                   
                }
                $imp = rtrim($imp, PHP_EOL);
            }
            
            $this->variable = array(
                'id'         => $id,
                'imp'        => $imp,
                'key'        => $key,
                'rules'      => $rules,
                'forms'      => $forms,
                'table'      => $table,
                'fields'     => $fields,
                'app_var'    => $app_var,
                'app_name'   => $app_name,
                'fillable'   => $fillable,
                'tablename'  => $tablename,
                'properties' => $properties
            );
            
            if ($has) {
                $this->parse('views', $app_var, 'create');
                $this->parse('views', $app_var, 'edit');
                $this->parse('views', $app_var, 'show', $backup);
                $this->parse('views', $app_var, 'index', $backup);
                $this->parse('views', $app_var, '_form', $backup);
                $this->parse('layouts', 'layouts', 'app', $backup);
                $this->classParse('controller', $app_name, 'controller', $backup);
            }
            $this->classParse('model', $app_name, 'model', $backup);
            $this->route_content .= $this->route();
            $i++;
        }
    }
    
    protected function makeFile($model, $file, $content, $backup = false)
    {
        if ($model == 'views') {
            $file = base_path('resources' . $this->ds . 'views' . $this->ds . $file . '.blade.php');
        } elseif ($model == 'controller') {
            $file = app_path('Http' . $this->ds . 'Controllers' . $this->ds . $file . 'Controller.php');
        } elseif ($model == 'model') {
            $file = app_path($file . '.php');
        } elseif ($model == 'layouts') {
            $file = base_path('resources' . $this->ds . 'views' . $this->ds . $file . '.blade.php');
            if (file_exists($file)) {
                return false;
            }
        }
        
        if (file_exists($file) && $backup == true) {
            if (file_exists($file . '~')) {
                for ($i = 1; $i <= 10; $i++) {
                    if (!file_exists($file . '~' . $i)) {
                        rename($file, $file . '~' . $i);
                        echo 'File Backup: ' . $file . '~' . $i . PHP_EOL;
                        break;
                    }
                }
            } else {
                rename($file, $file . '~');
                echo 'File Backup: ' . $file . '~' . PHP_EOL;
            }
            
        }
        is_dir(dirname($file)) or mkdir(dirname($file), 0775, true);
        file_put_contents($file, $content);
        echo $file . PHP_EOL;
    }
    
    protected function parse($name, $app, $file, $backup = false)
    {
        extract($this->variable);
        $content = include(__DIR__ . $this->ds . 'stencil' . $this->ds . $file . '.php');
        $this->makeFile($name, $app . $this->ds . $file, $content, $backup);
    }
    
    protected function classParse($name, $app, $file, $backup = false)
    {
        extract($this->variable);
        $content = include(__DIR__ . $this->ds . 'stencil' . $this->ds . $file . '.php');
        $this->makeFile($name, $app, $content, $backup);
    }
    
    protected function route()
    {
        extract($this->variable);
        $content = include(__DIR__ . $this->ds . 'stencil' . $this->ds . 'routes.php');
        return $content;
    }
    
    protected function routeParse($content)
    {
        $path = base_path('routes' . $this->ds . 'web.php');
        
        if (file_exists($path)) {
            $add = base_path('routes' . $this->ds . 'scaffold_routes.php');
        } else {
            $path = app_path('routes.php');
            $add  = app_path('scaffold_routes.php');
        }
        
        file_put_contents($add, $content);
	$crud = base_path('resources' . $this->ds . 'crud_code_help.txt');
		$mess ='';
		if(!empty($this->crud_code)){
		   file_put_contents($crud, $this->crud_code);
		   $mess = "| Created crud help code. Please open: $crud";
		}
        
        $app_path = explode($this->ds, base_path());
        $app_path = array_pop($app_path);
        
        echo PHP_EOL . PHP_EOL . str_repeat('-----', 15) . PHP_EOL . PHP_EOL;
        echo "| Route file created: $add " . PHP_EOL;
        echo "| Please open $path routes file" . PHP_EOL;
        echo "| add require('scaffold_routes.php');" . PHP_EOL;
        echo "| and run http://localhost/$app_path/public/yourRoute" . PHP_EOL;
        echo $mess;
        echo PHP_EOL . PHP_EOL . str_repeat('-----', 15) . PHP_EOL . PHP_EOL;
    }
    
    public function __destruct()
    {
        if (!empty($this->route_content)) {
            $this->routeParse('<?php' . $this->route_content);
        }
    }
    
}
