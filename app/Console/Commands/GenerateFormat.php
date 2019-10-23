<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GenerateHelper;
use Log;

class GenerateFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g {table_name} {--type=} {--overwrite} {--m} {--r} {--c} {--route} {--rm} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Format';

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

        $helper = new GenerateHelper();

        $tables = $helper->getTables();

        if(!in_array($table_name, $tables)) {
            $this->error('Table not found');
            exit;
        }

        $custom_names = [];

        $custom_names['workspace'] = "app";

        $type = GenerateHelper::TYPE_WRITE;
        if($options['type']) {
            $type = $options['type'];
        }

        $is_overwrite = $options['overwrite'];

        $names = $helper->getNames($table_name, $custom_names);
            
        $columns = $helper->getColumns($table_name);
        
        if($options['m'] || $options['all']) {
            $helper->generateModel($names, $columns, $type, $is_overwrite);
        }

        if($options['r'] || $options['all']) {
            $helper->generateRepresentation($names, $columns, $type, $is_overwrite);
        }

        if($options['r'] || $options['all']) {
            $helper->generateRepresentationCollection($names, $columns, $type, $is_overwrite);
        }

        if($options['route'] || $options['all']) {
            $helper->generateRoute($names, $columns, $type, $is_overwrite);
        }

        if($options['rm']) {
            $helper->generateRemoteModel($names, $columns, $type, $is_overwrite);
        }

        if($options['c'] || $options['all']) {
            $helper->generateController($names, $columns, $type, $is_overwrite);
        }
        
        $this->info('Success');
    }
}