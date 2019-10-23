<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Log;
use Route;
use Config;
use Schema;
use Storage;
use Validator;
use Carbon\Carbon;

class GenerateHelper 
{   
    public const TYPE_PRINT = 1;
    public const TYPE_PRINT_ALL = 2;
    public const TYPE_WRITE = 3;

    public function getModuleList() {

        $moduleList = [
            'user-module',
            'setting-module',
            'hr-employee-module',
            'currency-module',
            'promotion-module',
            'inventory-module',
            'order-module',
            'payment-module',
            'purchase-module',
            'merchant-module',
            'price-list-module',
            'membership-module',
            'sale-channel-pos-module',
        ];

        return $moduleList;
    }

    public function getIgnoreTables() {

        $ignore_tables = [
            'migrations', 
            'oauth_access_tokens', 
            'oauth_auth_codes', 
            'oauth_clients', 
            'oauth_personal_access_clients', 
            'oauth_refresh_tokens',
            'jobs',
            'failed_jobs'
        ];

        return $ignore_tables;
    }

    public function getIgnoreColumns() {

        $ignore_columns = [
            'id', 
            'status',
            'created_by', 
            'last_updated_by', 
            'organization_id', 
            'module_id', 
            'created_at',
            'updated_at',
            'authorized_by',
            'approved_by',
            'created_branch',
            'updated_branch',
            'created_ip',
            'updated_ip',
            'created_terminal',
            'updated_terminal'
        ];

        return $ignore_columns;
    }

    public function getNames($table_name, $custom_names=[]) {

        $name_object = new \stdClass();

        $name_object->table_name = $table_name;
        
        $name_object->model_name = studly_case(str_singular($table_name));

        $name_object->variable_name = camel_case(str_singular($table_name));

        $name_object->title = title_case(str_replace('_', ' ', str_singular($table_name)));

        $name_object->route = str_replace('_', '-', str_singular($table_name));

        
        $name_object->storage_path = base_path('storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public');
        
        $name_object->blueprint_path = storage_path('blueprints');
        
        if(array_key_exists('workspace', $custom_names) && $custom_names['workspace'] && $custom_names['workspace'] == "app") {
            $name_object->workspace = $custom_names['workspace'];

            $name_object->workspace_name = title_case($name_object->workspace);

            $name_object->namespace = title_case($name_object->workspace);

            $name_object->base_path = base_path("$name_object->workspace");
        } else {
            if(!is_dir(base_path('packages'))) {
                $this->error("Package Path not found");
            }

            $workspaceArr = array_slice(scandir(base_path('packages')), 2);
            $workspaceArr = array_diff($workspaceArr, array('swagger-info.php'));
            if(sizeof($workspaceArr) > 1) {
                if(array_key_exists('workspace', $custom_names) && $custom_names['workspace']) {
                    $name_object->workspace = $custom_names['workspace'];
                } else {
                    $this->error("Please Select Workspace");
                }
            } else {
                $name_object->workspace = array_last($workspaceArr);
            }

            if(!is_dir(base_path('packages' . DIRECTORY_SEPARATOR . $name_object->workspace))) {
                $this->error("Workspace Path not found");
            }

            $moduleArr = array_slice(scandir(base_path('packages' . DIRECTORY_SEPARATOR . $name_object->workspace)), 2);
            if(sizeof($moduleArr) > 1) {
                if(array_key_exists('module', $custom_names) && $custom_names['module']) {
                    $name_object->module = $custom_names['module'];
                } else {
                    $this->error("Please Select Module");
                }  
            } else {
                $name_object->module = array_last($moduleArr);
            }

            if(!is_dir(base_path('packages' . DIRECTORY_SEPARATOR . $name_object->workspace . DIRECTORY_SEPARATOR . $name_object->module))) {
                $this->error("Module Path not found");
            }
            
            $name_object->workspace_name = studly_case(str_replace('-', '_', $name_object->workspace));

            $name_object->module_name = studly_case(str_replace('-', '_', $name_object->module));

            $name_object->module_title = title_case(str_replace('-', ' ', $name_object->module));

            $name_object->module_base_title = studly_case(str_replace('-', ' ', str_replace_last('-module', '', $name_object->module)));
            
            $name_object->namespace = studly_case(str_replace('-', '_', $name_object->workspace)) . '\\' . $name_object->module_name;

            $name_object->base_path = base_path('packages' . DIRECTORY_SEPARATOR . $name_object->workspace . DIRECTORY_SEPARATOR . $name_object->module . DIRECTORY_SEPARATOR . 'src');
        }
        
        if($custom_names && sizeof($custom_names) > 0) {
            foreach($custom_names as $key => $custom_name) {
                if($custom_name && $custom_name != "string") {
                    $name_object->$key = $custom_name;
                }
            }
        }

        Log::debug((array)$name_object);

        return $name_object;
    }

    public function getTables() {

        $tables_in_db = DB::select('SHOW TABLES');
        $db = "Tables_in_" . env('DB_DATABASE');
        $tables = [];
        foreach($tables_in_db as $table) {
            $tables[] = $table->{$db};
        }

        return $tables;
    }
    
    public function getColumns($table_name, $extra_info=false) {

        $columnArr = [];

        $columns = DB::select(DB::raw("SHOW FIELDS FROM $table_name"));
        
        foreach($columns as $key => $column) {

            $column_object = new \stdClass();

            $column_object->name = $column->Field;
            $column_object->title = title_case(str_replace('_', ' ', $column->Field));
            $column_object->route = str_replace('_', '-', $column->Field);

            $column_object->is_primary_key = false;
            $column_object->is_foreign_key = false;
            if($column->Key == "PRI") {
                $column_object->is_primary_key = true;
            } else if($column->Key == "MUL") {
                $column_object->is_foreign_key = true;
            }

            $column_object->is_auto_increment = false;
            if($column->Extra == "auto_increment") {
                $column_object->is_auto_increment = true;
            }

            $column_object->is_null = false;
            if($column->Null == "YES") {
                $column_object->is_null = true;
            }

            $column_object->is_default_value = false;
            if($column->Default != NULL) {
                $column_object->is_default_value = true;
            }

            $column_object->is_nullable = false;
            if($column->Null == "YES" || $column->Default != NULL) {
                $column_object->is_nullable = true;
            }

            $column_object->default_value = $column->Default;
            
            $column_object->type = $column->Type;  

            $column_object->length = $this->getBetweenData($column->Type, '(', ')');

            # For All Type
            $column_object->is_char = false;
            $column_object->is_text = false;
            $column_object->is_int = false;
            $column_object->is_decimal = false;
            $column_object->is_timestamp = false;
            $column_object->is_datetime = false;

            if(str_contains($column->Type, 'char')) {
                $column_object->is_char = true;
            } else if(str_contains($column->Type, 'text')) {
                $column_object->is_text = true;
            } else if(str_contains($column->Type, 'int')) {
                $column_object->is_int = true;
            } else if(str_contains($column->Type, 'decimal')) {
                $column_object->is_decimal = true;
            } else if(str_contains($column->Type, 'timestamp')) {
                $column_object->is_timestamp = true;
            } else if(str_contains($column->Type, 'datetime')) {
                $column_object->is_datetime = true;
            } else {
                $this->error("Invalid Column Type $column->Type");
            }

            # For Custom Type
            $column_object->is_alpha = false;
            if($column_object->is_char || $column_object->is_text) {
                $column_object->is_alpha = true;
            }

            $column_object->is_number = false;
            if($column_object->is_int || $column_object->is_decimal) {
                $column_object->is_number = true;
            }

            $column_object->is_date = false;
            if($column_object->is_timestamp || $column_object->is_datetime) {
                $column_object->is_date = true;
            }

            # For Custom Value
            if($column_object->is_alpha || $column_object->is_date) {
                $column_object->swagger_type = "string";
            } else if($column_object->is_number) {
                $column_object->swagger_type = "integer";
            } else {
                $this->error("Invalid Swagger Type");
            }

            if($extra_info) {
                $column_object->extra_info = $this->getColumnInfo($table_name, $column->Field);
            }

            $columnArr[] = $column_object;
        }

        return $columnArr;
    }

    public function getColumnInfo($table_name, $column) {

        $con = DB::connection();
        $column = $con->getDoctrineColumn($table_name, $column); 

        $info_object = new \stdClass();
        
        $info_object->length = $column->getLength(); 

        $info_object->comment = $column->getComment();

        return $info_object;
    }

    public function getColumnNames($table_name) {

        $columnNames = [];
        $columns = DB::select(DB::raw("SHOW FIELDS FROM $table_name"));
        
        foreach($columns as $key => $column) {
            $columnNames[] = $column->Field;
        }

        return $columnNames;
    }

    public function getContents($path) {

        $path = explode('/', $path);
        $path = implode(DIRECTORY_SEPARATOR, $path);

        if(!file_exists($path)) {
            $this->error("File not found in path - $path");
        }

        $data = file_get_contents($path);

        return $data;
    }

    public function createContents($path, $filename=null, $file_data=null, $file_type='php', $is_overwrite=false) {
        
        $path = explode('/', $path);
        $path = implode(DIRECTORY_SEPARATOR, $path);
        
        if(!is_dir($path)) {
            if(!mkdir($path, 0700, true)) {
                $this->error("Fail To Create Directory");
            }
        }

        if($filename) {
            $full_path = $path . DIRECTORY_SEPARATOR . $filename;

            if($file_type) {
                $full_path = $full_path . '.' . $file_type;
            }

            if(file_exists($full_path) && !$is_overwrite) {
                $this->error("Already Exist File in $full_path");
            }

            if(!file_put_contents($full_path, $file_data)) {
                $this->error("Fail To Put Contents");
            }
        }

        return true;
    }

    public function replaceData($original_data, $replace_data=[]) {

        $data = strtr($original_data, $replace_data); 

        return $data;
    }

    public function getBetweenData($data, $start, $end) {
        
        $data = explode($start, $data);
        if(isset($data[1])) {
            $data = explode($end, $data[1]);
            return $data[0];
        }
        return '';
    }

    public function splitData($data, $max_length, $operator=" ") {

        $operator_length = $max_length - strlen($data);
        $start = 0;
        $separator = "";

        while($start < $operator_length) {
            $separator .= $operator;
            $start++;
        }

        return $data . $separator;
    }

    public function getTypeOfValue($value) {
        
        $type_object = new \stdClass();

        $data['value'] = $value;
        
        $type_object->is_comma = false;
        if(str_contains($value, ',')) {
            $type_object->is_comma = true;
            $data['value'] = array_first(explode(',', $value));
        } 
        
        $type_object->is_alpha = false;
        $type_object->is_number = false;
        $type_object->is_date = false;

        $v_alpha_num = Validator::make($data, [
            'value' => 'alpha_num'
        ]);

        $v_numeric = Validator::make($data, [
            'value' => 'numeric'
        ]);
        
        $v_date_format1 = Validator::make($data, [
            'value' => 'date_format:d/m/Y'
        ]);

        $v_date_format2 = Validator::make($data, [
            'value' => 'date_format:m/Y'
        ]);

        if(!$v_alpha_num->fails()) {
            $type_object->is_alpha = true;
        }

        if(!$v_numeric->fails()) {
            $type_object->is_number = true;
            $type_object->is_alpha = false;
        }

        if(!$v_date_format1->fails()) {
            $type_object->is_date = true;
            $type_object->is_alpha = false;
        }

        if(!$v_date_format2->fails()) {
            $type_object->is_date = true;
            $type_object->is_alpha = false;
        }
        
        return $type_object;
    }

    public function getReplaceData($names, $additional_params=[], $sign="@") {

        $replace_data = [];

        foreach($names as $key => $value) {
            $replace_data["$sign$key"] = $value;
        }

        if($additional_params && count($additional_params) > 0) {
            foreach($additional_params as $key => $value) {
                $replace_data["$key"] = $value;
            }
        }

        return $replace_data;
    }

    public function info($message, $title=false) {

        if($title) {
            print("\n-----$title-----\n"); 
        }
        print("\n$message\n");
    }

    public function error($message, $title=false) {

        if($title) {
            print("\n-----$title-----\n"); 
        }
        print("\n$message\n");
        exit;
    }

    public function generatePackageFolderAndFile($workspace, $module, $custom_names=[]) {
        
        $this->createContents(base_path("packages"));
        $this->createContents(base_path("packages/$workspace"));
        $this->createContents(base_path("packages/$workspace/$module"));
        $this->createContents(base_path("packages/$workspace/$module/src"));
        $this->createContents(base_path("packages/$workspace/$module/src/Config"));
        $this->createContents(base_path("packages/$workspace/$module/src/Contracts"));
        $this->createContents(base_path("packages/$workspace/$module/src/Controllers"));
        $this->createContents(base_path("packages/$workspace/$module/src/Database"));
        $this->createContents(base_path("packages/$workspace/$module/src/Database/migrations"));
        $this->createContents(base_path("packages/$workspace/$module/src/Database/seeds"));
        $this->createContents(base_path("packages/$workspace/$module/src/Jobs"));
        $this->createContents(base_path("packages/$workspace/$module/src/Logs"));
        $this->createContents(base_path("packages/$workspace/$module/src/Models"));
        $this->createContents(base_path("packages/$workspace/$module/src/Representations"));
        $this->createContents(base_path("packages/$workspace/$module/src/Resources"));
        $this->createContents(base_path("packages/$workspace/$module/src/Resources/lang"));
        $this->createContents(base_path("packages/$workspace/$module/src/Resources/lang/en"));
        $this->createContents(base_path("packages/$workspace/$module/src/Resources/views"));
        $this->createContents(base_path("packages/$workspace/$module/src/Resources"));
        $this->createContents(base_path("packages/$workspace/$module/src/Rmi"));
        $this->createContents(base_path("packages/$workspace/$module/src/Rmi/RemoteModels"));
        $this->createContents(base_path("packages/$workspace/$module/src/Routes"));

        $custom_names['workspace'] = $workspace;
        $custom_names['module'] = $module;
        
        $names = $this->getNames('demo', $custom_names);

        $file_data = $this->getContents("$names->blueprint_path/Demo/demo-constants.php");
        $this->createContents(base_path("packages/$workspace/$module/src/Config"), "$module.constants", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoContract.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src/Contracts"), "{$names->module_base_title}Contract", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/Controller.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src/Controllers"), "Controller", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/DatabaseSeeder.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src/Database/seeds"), "DatabaseSeeder", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/messages.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src/Resources/lang/en"), "messages", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/validation.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src/Resources/lang/en"), "validation", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/api.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src/Routes"), "api", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoServiceProvider.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module/src"), "{$names->module_name}ServiceProvider", $file_data);

        $original_data = $this->getContents("$names->blueprint_path/Demo/composer.json");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages/$workspace/$module"), "composer", $file_data, "json");
        
        $file_data = "vendor/";
        $this->createContents(base_path("packages/$workspace/$module"), ".gitignore", $file_data, NULL);
        
        $file_data = " ";
        $this->createContents(base_path("packages/$workspace/$module"), "readme", $file_data, "md");

        $original_data = $this->getContents("$names->blueprint_path/Demo/swagger-info.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);
        $this->createContents(base_path("packages"), "swagger-info", $file_data);
    }
    
    public function generateModel($names, $columns, $type, $is_overwrite=false) {
        
        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoModel.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);

        if($type == self::TYPE_PRINT) {
            $this->info("Nothing To Show");
        } else if($type == self::TYPE_PRINT_ALL) {
            $this->info($file_data);
        } else if($type == self::TYPE_WRITE) {
            $this->createContents("$names->base_path/Models", "$names->model_name", $file_data, 'php', $is_overwrite);
        }
    }

    public function generateRepresentation($names, $columns, $type, $is_overwrite=false) {
        
        # Representation
        $tab1 = "\t\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, ['created_at', 'updated_at'])) {
                continue;
            }

            if($column->is_date) {
                $view[]= "$tab1'$column->name' => \$this->when(\$this->$column->name, Carbon::parse(\$this->$column->name)->format('d/m/Y')),";
            } else {
                $view[]= "$tab1'$column->name' => \$this->when(\$this->$column->name, \$this->$column->name),";
            }
        }
        $view1 = implode("\n", $view);

        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoRepresentation.php");
        $replace_data = $this->getReplaceData($names, [
            '@view1' => $view1,
        ]);
        $file_data = $this->replaceData($original_data, $replace_data);

        if($type == self::TYPE_PRINT) {
            $this->info($view1, "Representation");
        } else if($type == self::TYPE_PRINT_ALL) {
            $this->info($file_data);
        } else if($type == self::TYPE_WRITE) {
            $this->createContents("$names->base_path/Representations", "{$names->model_name}Representation", $file_data, 'php', $is_overwrite);
        }
    }

    public function generateRepresentationCollection($names, $columns, $type, $is_overwrite=false) {
       
        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoRepresentationCollection.php");
        $replace_data = $this->getReplaceData($names);
        $file_data = $this->replaceData($original_data, $replace_data);

        if($type == self::TYPE_PRINT) {
            $this->info("Nothing To Show");
        } else if($type == self::TYPE_PRINT_ALL) {
            $this->info($file_data);
        } else if($type == self::TYPE_WRITE) {
            $this->createContents("$names->base_path/Representations", "{$names->model_name}RepresentationCollection", $file_data, 'php', $is_overwrite);
        }
    }

    public function generateRoute($names, $columns, $type, $is_overwrite=false) {
        
        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoRoute.php");
        $replace_data = $this->getReplaceData($names);
        $view1 = $this->replaceData($original_data, $replace_data);
        
        $original_data = $this->getContents(base_path('routes') . "/api.php");
        if(Str::contains($original_data, [$names->model_name . 'Controller'])) {
            $this->info("Already Exist Route");
            return;
        }
        $replace_data = $this->getReplaceData($names, [
            '#EndLine' => $view1
        ]);
        $file_data = $this->replaceData($original_data, $replace_data);
        
        if($type == self::TYPE_PRINT) {
            $this->info($view1, "Routes");
        } else if($type == self::TYPE_PRINT_ALL) {
            $this->info($file_data);
        } else if($type == self::TYPE_WRITE) {
            $this->createContents(base_path('routes'), "api", $file_data, 'php', $is_overwrite);
        }
    }

    public function generateRemoteModel($names, $columns, $type, $is_overwrite=false) {
        
        $original_data = $this->getContents("$names->blueprint_path/Demo/RemoteDemoModel.php");
        $replace_data = $this->getReplaceData($names, [
            '@workspace_name_upper' => strtoupper($names->workspace_name),
            '@module_upper' => strtoupper(str_replace('-', '_', $names->module)),
        ]);
        $file_data = $this->replaceData($original_data, $replace_data);

        if($type == self::TYPE_PRINT) {
            $this->info("Nothing To Show");
        } else if($type == self::TYPE_PRINT_ALL) {
            $this->info($file_data);
        } else if($type == self::TYPE_WRITE) {
            $this->createContents("$names->base_path/Rmi/RemoteModels", "Remote$names->model_name", $file_data, 'php', $is_overwrite);
        }
    }

    public function generateController($names, $columns, $type, $is_overwrite=false) {
        
        $ignore_columns = $this->getIgnoreColumns();
        $column_names = $this->getColumnNames($names->table_name);
        $variable_name_plural = str_plural($names->variable_name);
        
        # Input Query
        $tab1 = "\t\t";
        $view = [];
        if(in_array('module_id', $column_names)) {
            $view[] = "$tab1\$module_id = \$request->query('module_id');";
        }
        $view[] = "";
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            $view[] = "$tab1\$$column->name = \$request->query('$column->name');";
        }
        $view1 = implode("\n", $view);

        # Check Organization Plural
        $tab1 = "\t\t";
        $view = [];
        if(in_array('organization_id', $column_names)) {
            $view[] = "$tab1\$$variable_name_plural = \${$variable_name_plural}->where('organization_id', \$loggedinUser['organization_id']);";
            $view[] = "";
        }
        $view2 = implode("\n", $view);

        # Search
        $tab1 = "\t\t"; $tab2 = "\t\t\t"; $tab3 = "\t\t\t\t";
        $view = [];
        if(in_array('module_id', $column_names)) {
            array_push($view, 
                "",
                "{$tab1}if(\$module_id) {",
                "$tab2\$$variable_name_plural = \${$variable_name_plural}->where('module_id', \$module_id);",
                "$tab1}"
            );
        }
        array_push($view, 
            "",
            ""
        );
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            $view[] = "{$tab1}if(\$$column->name) {";
            if($column->is_alpha) {
                $view[] = "$tab2\$$variable_name_plural = \${$variable_name_plural}->where('$column->name', 'like', \"%{\$$column->name}%\");";
            } else if($column->is_number) {
                $view[] = "$tab2\$$variable_name_plural = \${$variable_name_plural}->where('$column->name', \$$column->name);";
            } else if($column->is_date) {
                $view[] = "$tab2\$$variable_name_plural = \${$variable_name_plural}->whereRaw('DATE($column->name) = STR_TO_DATE(?, \"%d/%m/%Y\")', [ \$$column->name ]);";
            }
            $view[] = "$tab1}";
            $view[] = "";
        }
        $view3 = implode("\n", $view);

        # Excel Title
        $tab1 = "\t\t\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            $view[] = "$tab1'$column->title',";
        }
        $view4 = implode("\n", $view);

        # Excel Value
        $tab1 = "\t\t\t\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            if($column->is_date) {
                $view[] = "$tab1\$value['$column->name'] ? Carbon::parse(\$value['$column->name'])->format('d/m/Y') : \"\",";
            } else if($column->is_alpha) {
                $view[] = "$tab1\$value['$column->name'],";
            } else if($column->is_number) {
                if($column->is_int) {
                    $view[] = "$tab1\$value['$column->name'],";
                } else if($column->is_decimal) {
                    $view[] = "{$tab1}number_format(\$value['$column->name'], 2, '.', ','),";
                }
            }
        }
        $view5 = implode("\n", $view);

        # Swagger Post
        $tab1 = "\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            if($column->is_number) {
                $view[] = "$tab1*                @OA\Property(property = \"$column->name\", type = \"integer\"),";
            } else {
                $view[] = "$tab1*                @OA\Property(property = \"$column->name\", type = \"string\"),";
            }
        }
        $view6 = implode("\n", $view);

        $tab1 = "\t\t";$tab2 = "\t\t\t";
        $view = [];
        $view[] = "$tab1(new GeneralLogLib)";
        $view[] = "{$tab2}->addTag(['{$names->model_name}Controller', 'store'])";
        $view[] = "{$tab2}->addModelName(\"{$names->model_name}\")";
        $view[] = "{$tab2}->addDescription(\"Add {$names->model_name}\")";
        $view[] = "{$tab2}->log(\$loggedinUser, GeneralLogLib::EVENT_CREATED, \${$names->variable_name}, \${$names->variable_name}, \$request);";
        $view9_1 = implode("\n", $view);

        $tab1 = "\t\t";$tab2 = "\t\t\t";
        $view = [];
        $view[] = "$tab1(new GeneralLogLib)";
        $view[] = "{$tab2}->addTag(['{$names->model_name}Controller', 'update'])";
        $view[] = "{$tab2}->addModelName(\"{$names->model_name}\")";
        $view[] = "{$tab2}->addDescription(\"Update {$names->model_name}\")";
        $view[] = "{$tab2}->log(\$loggedinUser, GeneralLogLib::EVENT_UPDATED, \$before_{$names->variable_name}, \${$names->variable_name}, \$request);";
        $view11_1 = implode("\n", $view);

        # Validation
        $tab1 = "\t\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            if(!$column->is_nullable) {
                if($column->is_alpha) {
                    $view[] = "$tab1'$column->name' => 'required|string',";
                } else if($column->is_number) {
                    if($column->is_int) {
                        $view[] = "$tab1'$column->name' => 'required|integer',";
                    } else if($column->is_decimal) {
                        $view[] = "$tab1'$column->name' => 'required|numeric',";
                    }
                } else if($column->is_date) {
                    $view[] = "$tab1'$column->name' => 'required|date_format:d/m/Y',";
                }
            } else {
                if($column->is_alpha) {
                    $view[] = "$tab1'$column->name' => 'nullable|string',";
                } else if($column->is_number) {
                    if($column->is_int) {
                        $view[] = "$tab1'$column->name' => 'nullable|integer',";
                    } else if($column->is_decimal) {
                        $view[] = "$tab1'$column->name' => 'nullable|numeric',";
                    }
                } else if($column->is_date) {
                    $view[] = "$tab1'$column->name' => 'nullable|date_format:d/m/Y',";
                }
            }
        }
        $view7 = implode("\n", $view);

        # Create Data
        $tab1 = "\t\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }
            
            if(!$column->is_nullable) {
                $view[] = "$tab1'$column->name' => \$data['$column->name'],";
            }
        }
        array_push($view, 
            "",
            "$tab1'status' => Config::get('constants.STATUS.ACTIVE'),", 
            "$tab1'created_by' => \$loggedinUser['id'],",
            "$tab1'last_updated_by' => \$loggedinUser['id'],"
        );
        if(in_array('organization_id', $column_names)) {
            array_push($view, 
                "$tab1'organization_id' => \$loggedinUser['organization_id'],"
            );
        }
        if(in_array('module_id', $column_names)) {
            array_push($view, 
                "$tab1'module_id' => Config::get('constants.MODULE_ID'),"
            );
        }
        $view8 = implode("\n", $view);

        # Change Data
        $tab1 = "\t\t"; $tab2 = "\t\t\t";
        $view = [];
        $view[] = "";
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }

            if($column->is_nullable) {
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
            }
        }
        $view9 = implode("\n", $view);

        # Update Validation
        $tab1 = "\t\t\t";
        $view = [];
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                continue;
            }
            
            if(!$column->is_nullable) {
                if($column->is_alpha) {
                    $view[] = "$tab1'$column->name' => 'nullable|string',";
                } else if($column->is_number) {
                    if($column->is_int) {
                        $view[] = "$tab1'$column->name' => 'nullable|integer',";
                    } else if($column->is_decimal) {
                        $view[] = "$tab1'$column->name' => 'nullable|numeric',";
                    }
                } else if($column->is_date) {
                    $view[] = "$tab1'$column->name' => 'nullable|date_format:d/m/Y',";
                }
            } else {
                if($column->is_alpha) {
                    $view[] = "$tab1'$column->name' => 'nullable|string',";
                } else if($column->is_number) {
                    if($column->is_int) {
                        $view[] = "$tab1'$column->name' => 'nullable|integer',";
                    } else if($column->is_decimal) {
                        $view[] = "$tab1'$column->name' => 'nullable|numeric',";
                    }
                } else if($column->is_date) {
                    $view[] = "$tab1'$column->name' => 'nullable|date_format:d/m/Y',";
                }
            }
        }
        $view10 = implode("\n", $view);

        # Update Data
        $tab1 = "\t\t"; $tab2 = "\t\t\t"; $tab3 = "\t\t\t\t";
        $view = [];
        array_push($view,
            "$tab1\$updateData = [",
            "$tab2'last_updated_by' => \$loggedinUser['id']",
            "$tab1];",
            ""
        );
        foreach($columns as $column) {
            if(in_array($column->name, $ignore_columns)) {
                if($column->name != "status") {
                    continue;
                }
            }

            if($column->is_null && !$column->is_date) {
                $view[] = "{$tab1}if(array_key_exists('$column->name', \$data)) {";
                $view[] = "$tab2\$updateData['$column->name'] = \$data['$column->name'] ?: null;";
                $view[] = "$tab1}";
            } else {
                $view[] = "{$tab1}if(array_key_exists('$column->name', \$data) && \$data['$column->name']) {";
                if($column->is_date) {
                    $view[] = "$tab2\$updateData['$column->name'] = Carbon::createFromFormat('d/m/Y', \$data['$column->name']);";
                } else {
                    $view[] = "$tab2\$updateData['$column->name'] = \$data['$column->name'];";
                }
                $view[] = "$tab1}";
            }
            $view[] = "";
        }
        // array_push($view,
        //     "$tab1\$updateData['last_updated_by'] = \$loggedinUser['id'];"
        // );
        $view11 = implode("\n", $view);

        # Check Organization
        $tab1 = "\t\t";
        $view = [];
        if(in_array('organization_id', $column_names)) {
            $view[] = "$tab1\$$names->variable_name = \$$names->variable_name->where('organization_id', \$loggedinUser['organization_id']);";  
            $view[] = "";
        }
        $view12 = implode("\n", $view);
        
        $original_data = $this->getContents("$names->blueprint_path/Demo/DemoController.php");
        $replace_data = $this->getReplaceData($names, [
            '@variable_name_plural' => str_plural($names->variable_name),
            '@view1' => $view1, 
            '@view2' => $view2, 
            '@view3' => $view3, 
            '@view4' => $view4, 
            '@view5' => $view5, 
            '@view6' => $view6, 
            '@view7' => $view7, 
            '@view8' => $view8, 
            '@view9' => $view9, 
            '@view10' => $view10,
            '@view11' => $view11,
            '@view12' => $view12,
            '@view9_1' => $view9_1,
            '@view11_1' => $view11_1,
        ]);
        $file_data = $this->replaceData($original_data, $replace_data);

        if($type == self::TYPE_PRINT) {
            $this->info($view1, "Input Query");
            $this->info($view2, "Check Organization Plural");
            $this->info($view3, "Search");
            $this->info($view4, "Excel Title");
            $this->info($view5, "Excel Value");
            $this->info($view6, "Swagger Post");
            $this->info($view7, "Validation");
            $this->info($view8, "Create Data");
            $this->info($view9, "Change Data");
            $this->info($view10, "Update Validation");
            $this->info($view11, "Update Data");
            $this->info($view12, "Check Organization");
        } else if($type == self::TYPE_PRINT_ALL) {
            $this->info($file_data);
        } else if($type == self::TYPE_WRITE) {
            $this->createContents("$names->base_path/Http/Controllers", "{$names->model_name}Controller", $file_data, 'php', $is_overwrite);
        }
    }

    public function generateDummyData($table_name, $total=10) {

        $ignore_columns = [
            'id', 
        ];
        
        $custom_values = [
            'status' => 1,
            'created_by' => 1,
            'last_updated_by' => 1,
            'organization_id' => 1,
            'module_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $columns = $this->getColumns($table_name);

        $num = 1;
        $start = 1; 
        $data = [];
        
        while($start <= $total) {
            
            if($num == 10) $num = 1;

            $row_data = [];
            foreach($columns as $key => $column) {

                if(in_array($column->name, $ignore_columns)) {
                    continue;
                }
                
                if($column->is_alpha) {
                    $row_data[$column->name] = $column->title . ' ' . $start;
                } else if($column->is_number) {
                    if($column->is_int) {
                        if($column->length <= 10) {
                            $row_data[$column->name] = 1 * $num;
                        } else {
                            $row_data[$column->name] = 10 * $num;
                        }
                    } else if($column->is_decimal) {
                        $row_data[$column->name] = 1000 * $num;
                    }
                } else if($column->is_date) {
                    $row_data[$column->name] = Carbon::now();
                } 

                if(array_key_exists($column->name, $custom_values) && $custom_values[$column->name]) {
                    $row_data[$column->name] = $custom_values[$column->name];
                }
            }
        
            $start++;
            $num++;
            $data[] = $row_data;
        }

        DB::table($table_name)->insert($data);
    }

    public function generateScopesForSeeder($module_name) {

        $tables = $this->getTables();
        $ignore_tables = $this->getIgnoreTables();

        # Scopes For Seeder
        $tab1 = "";$tab2 = "    ";
        $view = [];
        $view[] = "/*------------------ $module_name -----------------*/";
        $view[] = "";
        // foreach($tables as $table) {
        //     if(in_array($table, $ignore_tables)) {
        //         continue;
        //     }
            if(!in_array($module_name, $tables)) {
                $this->error("Table not found", "Scopes For Seeder");
            }

            $table = $module_name;

            $route = str_replace('_', '-', str_singular($table));
            $title = title_case(str_replace('_', ' ', str_singular($table)));

            $view[] = "# $route";
            $view[] = "{$tab1}[";
            $view[] = "$tab2'scope'      => '{$route}',";
            $view[] = "$tab2'scope_name' => '$title'";
            $view[] = "$tab1],";

            $view[] = "{$tab1}[";
            $view[] = "$tab2'scope'      => '{$route}.read',";
            $view[] = "$tab2'scope_name' => 'Readonly for $title'";
            $view[] = "$tab1],";

            $view[] = "{$tab1}[";
            $view[] = "$tab2'scope'      => '$route.write',";
            $view[] = "$tab2'scope_name' => 'Writable(Create/Update) for $title'";
            $view[] = "$tab1],";

            $view[] = "{$tab1}[";
            $view[] = "$tab2'scope'      => '$route.remove',";
            $view[] = "$tab2'scope_name' => 'Removable for $title'";
            $view[] = "$tab1],";
        // }
        $view1 = implode("\n", $view);
        $this->info($view1, "Scopes For Seeder");
    }
    
    public function generateServerConfig($ip=null, $port=null, $source=null, $destination=null) {

        if(!$ip) {
            $ip = "localhost";
        }
        if(!$port) {
            $port = 8001;
        }

        $modules = $this->getModuleList();

        # For Copy
        $tab1 = "";
        $view = [];
        $view[] = "cd $destination";
        $view[] = "";
        foreach($modules as $module) {
            
            $view[] = "mkdir -p ./jp-$module/packages && cp -Rf $source/jp-$module/packages/* ./jp-$module/packages";
            $view[] = "";
        }
        $view1 = implode("\n", $view);
        $this->info($view1, "For Copy");
        
        # For UI
        $tab1 = "";
        $ui_port = $port;
        $view = [];
        foreach($modules as $module) {
            
            $view[] = "'$module' : 'http://$ip:$ui_port/api/v1',";
            $ui_port++;
        }
        $view2 = implode("\n", $view);
        $this->info($view2, "For UI");

        # For ENV
        $tab1 = "";
        $env_port = $port;
        $view = [];
        foreach($modules as $module) {

            $module_name = strtoupper(str_replace('-', '_', $module));
            
            $view[] = "JARPLAY_{$module_name}_ENDPOINT=http://$ip:$env_port/api/v1/$module";
            $env_port++;
        }
        $view3 = implode("\n", $view);
        $this->info($view3, "For ENV");
    }

    public function generateQueryToCopyData($table_name, $from_email, $to_email, $status=false) {

        $from_user = DB::table('users')->where('email', $from_email);
        if($from_user->doesntExist()) {
            $this->error("Invalid From Email");
        }
        $from_user = $from_user->first();

        $to_user = DB::table('users')->where('email', $to_email);
        if($to_user->doesntExist()) {
            $this->error("Invalid To Email");
        }
        $to_user = $to_user->first();

        $data = DB::table($table_name)->where('organization_id', $from_user->organization_id);
        if($status && $status != null) {
            $data = $data->where('status', $status);
        }
        if($data->exists()) {
            
            $data = $data->get();

            $column_names = $this->getColumnNames($table_name);
            $columns = $this->getColumns($table_name);
            $column_types = [];
            foreach ($columns as $key => $column) {
                $column_types[$column->name] = $column->is_number;
            }
            
            $view1 = "INSERT INTO `$table_name` (";

            $tab1 = "";
            $view = [];
            foreach($column_names as $column_name) {
                if(in_array($column_name, ['id'])) {
                    continue;
                }
                $view[] = "`$column_name`";
            }
            $view2 = implode(", ", $view);
            
            $view3 = ")";

            $view4 = "VALUES";

            $view = [];
            foreach($data as $key => $value) {
                
                $sub_view = [];
                foreach($value as $key1 => $value1) {
                    
                    if($key1 == "id") {
                        continue;
                    } else if($key1 == "created_by") {
                        $sub_view[] = $to_user->id;
                    } else if($key1 == "last_updated_by") {
                        $sub_view[] = $to_user->id;
                    } else if($key1 == "organization_id") {
                        $sub_view[] = $to_user->organization_id;
                    } else if($key1 == "created_at") {
                        $sub_view[] = "'" . Carbon::now() . "'";
                    } else if($key1 == "updated_at") {
                        $sub_view[] = "'" . Carbon::now() . "'";
                    } else if($key1 == "status") {
                        $sub_view[] = "1";
                    } else {
                        if($value1) {
                            if($column_types[$key1]) {
                                $sub_view[] = $value1;
                            } else {
                                $sub_view[] = "'$value1'";
                            }
                        } else {
                            if($value1 == 0) {
                                $sub_view[] = 0;
                            } else {
                                $sub_view[] = "NULL";
                            }
                        }
                    }
                }
                $sub_view = implode(", ", $sub_view);

                $view[] = "( $sub_view )";
            }
            $view5 = implode(",\n", $view);

            $view6 = ";";

            $this->info($view1 . $view2 . $view3);
            $this->info($view4);
            $this->info($view5 . $view6);
        }
    }
}