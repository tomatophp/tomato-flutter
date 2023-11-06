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
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class TomatoFlutterGenerateApp extends Command
{
    use RunCommand;
    use HandleStub;

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
        $name = text(
            label: 'What is your app name?',
            placeholder: 'tomato',
            required: true
        );
        $process = new Process([
            'flutter',
            'create',
            $name
        ]);
        $process->setWorkingDirectory(base_path('/flutter'));
        $process->run();

        info('Generate MVC Files Now...');

        $assetsFolder = base_path('/flutter/' . $name .'/assets');
        $libFolder = base_path('/flutter/' . $name .'/lib');
        $testFolder = base_path('/flutter/' . $name .'/test');
        $packagesFolder = base_path('/flutter/' . $name .'/packages');
        $pubspecFile = base_path('/flutter/' . $name .'/pubspec.yaml');

        if(!File::exists($assetsFolder)){
            File::copyDirectory(__DIR__.'/../../share/assets', $assetsFolder);
        }
        if(!File::exists($libFolder)){
            File::copyDirectory(__DIR__.'/../../share/lib', $libFolder);
        }
        if(!File::exists($testFolder)){
            File::copyDirectory(__DIR__.'/../../share/test', $testFolder);
        }
        if(!File::exists($packagesFolder)){
            File::copyDirectory(__DIR__.'/../../share/packages', $packagesFolder);
        }
        if(File::exists($pubspecFile)){
            File::delete($pubspecFile);
        }

        $this->generateStubs(
            __DIR__ .'/../../stubs/pubspec.stub',
            base_path('/flutter/' . $name .'/pubspec.yaml'),
            [
                'name' => $name,
            ]
        );

        $appConfigPath = base_path('/flutter/' . $name . '/lib/config/Config.dart');

        //Delete App Service If Exists
        if (File::exists($appConfigPath)) {
            File::delete($appConfigPath);
        }
        //Create App Service
        $this->generateStubs(
            __DIR__ .'/../../stubs/config/Config.stub',
            $appConfigPath,
            [
                "app_name" => $name,
                "url" => url('/api'),
            ]
        );

        if($process->isSuccessful()){
            $process = new Process([
                'flutter',
                'pub',
                'get'
            ]);
            $process->setWorkingDirectory(base_path('/flutter/' . $name));
            $process->run();
        }


        info('Your app is ready!');
    }
}
