<?php
namespace Scaffold\Builder;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;


class Scaffold extends Command
{
    use Relation;
    use CreateBuilder;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:scaffold';
    
    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Make scaffold all Model, Controller, Views.';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
		 
        if (count($this->relate()) == 0 && count($this->noRelate()) == 0) {
            $this->info('Not found tables in Database. Please; before scaffolding, create table or tables.');
            return false;
        }
       
        if ($this->confirm('Generate all controller, model and views? [y|N]')) {
            $this->info(str_repeat("=", 50));
		$backup = false;
            if ($this->confirm('Notice: Are you backed up if exists files? [y|N]')) {
                $backup = true;
            }
        } else {
            echo 'End create scaffold.' . PHP_EOL;
            return false;
        }
		
        $this->_create($this->hasmany,'_has_many', $backup);
        $this->_create($this->belongsto, '_belongs_to', $backup, false);
        $this->_create($this->noRelate(), false, $backup);
        
    }
    
 
}
