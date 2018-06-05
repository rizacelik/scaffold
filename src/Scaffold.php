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
    public function handle()
    {

        $relate   = $this->relate();
        $noRelate = $this->noRelate();

        if (count($relate) == 0 && count($noRelate) == 0) {
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

        $this->crud_code = $this->template;
        $this->_create($relate, true, $backup);
        $this->_create($noRelate, false, $backup);

    }

}
