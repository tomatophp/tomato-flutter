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
        try {
            $process = new Process([
                'flutter',
                'emulators'
            ]);
            $process->setWorkingDirectory(base_path('/flutter'));
            $process->run();
        }catch (\Exception $exception) {
            error('please install flutter first, run "flutter doctor" to check if flutter installed');
        }

        try {
            $output = Str::of(Str::of($process->getOutput())->explode("\n")[2])->explode(' • ');
            $device = $output[0];

            $process = new Process([
                'flutter',
                'emulators',
                '--launch',
                $device
            ]);
            $process->setWorkingDirectory(base_path('/flutter'));
            $process->run();
        }
        catch (\Exception $exception){
            error('there is no emulators on your machine, please run "flutter emulators --create" to create new one');
        }


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
        if(File::exists($libFolder)){
            File::deleteDirectory($libFolder);
            File::copyDirectory(__DIR__.'/../../share/lib', $libFolder);
        }
        if(File::exists($testFolder)){
            File::deleteDirectory($testFolder);
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