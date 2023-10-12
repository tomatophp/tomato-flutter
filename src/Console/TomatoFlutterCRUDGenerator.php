<?php

namespace TomatoPHP\TomatoFlutter\Console;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use TomatoPHP\ConsoleHelpers\Traits\HandleStub;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;
use TomatoPHP\TomatoFlutter\Services\Generator\FlutterCRUDGenerator;
use function Laravel\Prompts\error;
use function Laravel\Prompts\search;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class TomatoFlutterCRUDGenerator extends Command
{
    use RunCommand;
    use HandleStub;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tomato-flutter:crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate a full crud for your flutter app from your laravel tables';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {

        $checkIfFlutterExists = File::exists(base_path('/flutter'));
        if(!$checkIfFlutterExists){
            File::makeDirectory(base_path('/flutter'));
        }
        $apps = collect(File::directories(base_path('/flutter')))->map(function ($item){
            return Str::of($item)->remove(base_path('/flutter') .'/');
        });
        $name = suggest(
            label:'What is your app name?',
            placeholder:'tomato',
            options: fn (string $value) => strlen($value) > 0
                ? collect($apps)->filter(function ($item, $key) use ($value){
                    return Str::contains($item, $value) ? $item : null;
                })->toArray()
                : [],
            validate: fn (string $value) => match (true) {
                strlen($value) < 1 => "Sorry this filed is required!",
                default => null
            },
            scroll: 10
        );

        $module = text(
            label: 'What is your module name?',
            placeholder: 'Blog',
            required: true
        );


        $tables = collect(\DB::select('SHOW TABLES'))->map(function ($item){
            return $item->{'Tables_in_'.config('database.connections.mysql.database')};
        })->toArray();

        $tableName = search(
            label: 'Please input your table name you went to create CRUD?',
            options: fn (string $value) => strlen($value) > 0
                ? collect($tables)->filter(function ($item, $key) use ($value){
                    return Str::contains($item, $value) ? (string)$item : null;
                })->toArray()
                : [],
            placeholder: "ex: users",
            scroll: 10
        );

        if(is_numeric($tableName)){
            $tableName = $tables[$tableName];
        }

        $checkIfModuleExists = File::exists(base_path('/flutter/' . $name . '/lib/app/modules/' . Str::of($module)->camel()->ucfirst()->toString()));
        if(!$checkIfModuleExists){
            $process = new Process([
                'flutter',
                'pub',
                'run',
                'modulr:generate',
                Str::of($module)->camel()->ucfirst()->toString()
            ]);
            $process->setWorkingDirectory(base_path('/flutter/' . $name));
            $process->run();
        }

        $generator = new FlutterCRUDGenerator($name, $module, $tableName);
        $generator->generate();
    }
}
