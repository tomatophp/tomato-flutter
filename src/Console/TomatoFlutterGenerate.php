<?php

namespace TomatoPHP\TomatoFlutter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;
use function Laravel\Prompts\text;

class TomatoFlutterGenerate extends Command
{
    use RunCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tomato-flutter:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate new flutter app';

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
        $name = text('What is your app name?');
        $process = new Process([
            'flutter',
            'create',
            $name
        ]);
        $process->setWorkingDirectory(base_path('/flutter'));
        $process->run();

        if($process->isSuccessful()){
            \Laravel\Prompts\info($process->getOutput());
            $process = new Process([
                'flutter',
                'pub',
                'add',
                'get'
            ]);
            $process->setWorkingDirectory(base_path('/flutter/' . $name));
            $process->run();
            \Laravel\Prompts\info($process->getOutput());
        }



        $this->artisanCommand(["optimize:clear"]);
        \Laravel\Prompts\info('Generate MVC Files Now');

        $checkIfNotExistsFolders = [
            'app' => 'dir',
            'app/Http'=> 'dir',
            'app/Http/Controllers'=> 'dir',
            'app/Http/Controllers/.gitkeep'=> 'file',
            'app/Http/Middlewares'=> 'dir',
            'app/Http/Middlewares/.gitkeep'=> 'file',
            'app/Http/Requests'=> 'dir',
            'app/Http/Requests/.gitkeep'=> 'file',
            'app/Services'=> 'dir',
            'app/Services/.gitkeep'=> 'file',
            'app/Models'=> 'dir',
            'app/Models/.gitkeep'=> 'file',
            'app/Providers'=> 'dir',
            'app/Providers/.gitkeep'=> 'file',
            'app/Exceptions'=> 'dir',
            'app/Exceptions/.gitkeep'=> 'file',
            'app/Helpers'=> 'dir',
            'app/Helpers/.gitkeep'=> 'file',
            'app/Events'=> 'dir',
            'app/Events/.gitkeep'=> 'file',
            'app/Listeners'=> 'dir',
            'app/Listeners/.gitkeep'=> 'file',
            'app/Notifications'=> 'dir',
            'app/Notifications/.gitkeep'=> 'file',
            'config'=> 'dir',
            'config/images.dart'=> 'file',
            'config/colors.dart'=> 'file',
            'lang'=> 'dir',
            'lang/ar.json'=> 'file',
            'lang/en.json'=> 'file',
            'Modules'=> 'dir',
            'Modules/.gitkeep'=> 'file',
            'resources'=> 'dir',
            'resources/assets'=> 'dir',
            'resources/assets/images'=> 'dir',
            'resources/assets/images/.gitkeep'=> 'file',
            'resources/assets/fonts'=> 'dir',
            'resources/assets/fonts/.gitkeep'=> 'file',
            'resources/views'=> 'dir',
            'resources/views/welcome.dart'=> 'file',
            'routes'=> 'dir',
            'routes/web.dart'=> 'file',
            'storage'=> 'dir',
            'storage/logs'=> 'dir',
            'storage/logs/.gitkeep'=> 'file',
            'storage/app/public'=> 'dir',
            'storage/app/public/.gitkeep'=> 'file',
            'tests'=> 'dir',
            'tests/.gitkeep'=> 'file',
        ];

        foreach ($checkIfNotExistsFolders as $path=>$type){
            $isExistsItem = File::exists(base_path('/flutter/'.$name.'/lib/'.$path));
            if(!$isExistsItem){
                if($type === 'dir'){
                    File::makeDirectory(base_path('/flutter/'.$name.'/lib/'.$path), 0777, true);
                }
                else {
                    if(Str::of($path)->contains('.json')){
                        File::put(base_path('/flutter/'.$name.'/lib/'.$path), '{}');
                    }
                    else {
                        File::put(base_path('/flutter/'.$name.'/lib/'.$path), '');
                    }

                }
            }
        }

        info('Your app is ready!');
    }
}
