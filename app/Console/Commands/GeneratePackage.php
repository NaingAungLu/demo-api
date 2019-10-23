<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GenerateHelper;
use Log;

class GeneratePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:package {workspace} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Package';

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
        $workspace = $this->argument('workspace');
        $module = $this->argument('module');
         
        $helper = new GenerateHelper();

        $helper->generatePackageFolderAndFile($workspace, $module);
            
        $this->info('Success');
    }
}
