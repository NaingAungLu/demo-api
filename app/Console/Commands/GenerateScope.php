<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GenerateHelper;
use Log;

class GenerateScope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:scope {module_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Scope';

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
        $module_name = $this->argument('module_name');
        
        $helper = new GenerateHelper();

        $helper->generateScopesForSeeder($module_name);
            
        $this->info('Success');
    }
}
