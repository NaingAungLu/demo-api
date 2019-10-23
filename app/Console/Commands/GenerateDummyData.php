<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GenerateHelper;
use Log;

class GenerateDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:d {table_name} {--total=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Dummy Data';

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
        $table_name = $this->argument('table_name');
        $options = $this->options();

        $total = 10;
        if($options['total']) {
            $total = $options['total'];
        }

        $helper = new GenerateHelper();

        $ignore_tables = $helper->getIgnoreTables();
        $tables = $helper->getTables();
        $table_names = $tables;

        if($table_name != "all") {
            $table_names = explode(",", $table_name);
        }
        
        foreach($table_names as $key => $table) {

            if(in_array($table, $ignore_tables)) {
                continue;
            }
            
            if(!in_array($table, $tables)) {
                $this->error('Table not found');
                exit;
            }

            $helper->generateDummyData($table, $total);
        }
        
        $this->info('Success');
    }
}
