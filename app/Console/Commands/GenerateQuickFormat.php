<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\GenerateHelper;

use App\Library\ExcelLib;

use DB;
use Log;

class GenerateQuickFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q {table_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Quick Format';

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

        $names = $helper->getNames($table_name, ['workspace' => 'app']);
        $columns = $helper->getColumns($table_name);

        $ignore_columns = [
            'id', 
            'status',
            'created_by', 
            'last_updated_by', 
            'created_at',
            'updated_at',
        ];

        $tab1 = "\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            $view[] = "'$column->name' => \$data['$column->name'],";
        }
        array_push($view,
            "",
            "'status' => Config::get('constants.STATUS.ACTIVE'),",
            "'created_by' => \$loggedinUser['id'],",
            "'last_updated_by' => \$loggedinUser['id']"
        );
        $view1 = implode("\n", $view);
        print("\n$view1\n");


        $tab1 = "\t";$tab2 = "\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            //if($column->is_nullable) {
                if($column->is_date) {
                    $view[] = "{$tab1}if(array_key_exists('$column->name', \$data) && \$data['$column->name']) {";
                    $view[] = "$tab2\$new$names->model_name['$column->name'] = Carbon::createFromFormat('d/m/Y', \$data['$column->name']);";
                    $view[] = "$tab1}";
                    $view[] = "";
                } else {
                    $view[] = "{$tab1}if(array_key_exists('$column->name', \$data) && \$data['$column->name']) {";
                    $view[] = "$tab2\$new$names->model_name['$column->name'] = \$data['$column->name'];";
                    $view[] = "$tab1}";
                    $view[] = "";
                }
            // }
        }
        $view1 = implode("\n", $view);
        print("\n$view1\n");
    }
}
