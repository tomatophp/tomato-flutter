<?php

namespace TomatoPHP\TomatoFlutter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use TomatoPHP\ConsoleHelpers\Traits\HandleStub;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;
use function Laravel\Prompts\error;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class TomatoFlutterGenerateService extends Command
{
    use RunCommand;
    use HandleStub;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tomato-flutter:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate new service into flutter app';

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

        $modules = collect(File::directories(base_path('/flutter/' . $name . '/lib/app/modules')))->map(function ($item) use ($name){
            return Str::of($item)->remove(base_path('/flutter/' . $name . '/lib/app/modules') .'/');
        });

        $module = suggest(
            label:'What is your module name?',
            placeholder:'tomato',
            options: fn (string $value) => strlen($value) > 0
                ? collect($modules)->filter(function ($item, $key) use ($value){
                    return Str::contains($item, $value) ? $item : null;
                })->toArray()
                : [],
            validate: fn (string $value) => match (true) {
                strlen($value) < 1 => "Sorry this filed is required!",
                default => null
            },
            scroll: 10

        );


        $service = text(
            label: 'What is your service name?',
            placeholder: 'Home',
            required: true
        );

        $process = new Process([
            'flutter',
            'pub',
            'run',
            'modulr:service',
            $service,
            '--on='.$module
        ]);
        $process->setWorkingDirectory(base_path('/flutter/' . $name));
        $process->run();

        \Laravel\Prompts\info($process->getOutput());
    }
}
