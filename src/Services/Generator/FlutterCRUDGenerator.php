<?php

namespace TomatoPHP\TomatoFlutter\Services\Generator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use TomatoPHP\ConsoleHelpers\Traits\HandleStub;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GenerateCols;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GenerateModel;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GenerateModule;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GeneratePages;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GenerateRoutes;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GenerateServices;
use TomatoPHP\TomatoFlutter\Services\Generator\Concerns\GenerateControllers;

class FlutterCRUDGenerator
{
    private string $module;
    private string $moduleLower;
    private string $table;
    private string $tableUpper;
    private string $stubPath;
    private array $cols;

    //Handler
    use HandleStub;

    //Generate Classes
    use GenerateCols;
    use GenerateModel;
    use GenerateServices;
    use GenerateControllers;
    use GeneratePages;
    use GenerateRoutes;
    use GenerateModule;

    private Connection $connection;

    /**
     * @param string $tableName
     * @param string|bool|null $moduleName
     * @throws Exception
     */
    public function __construct(
        private string $appName,
        private string $appModuleName,
        private string $tableName
    ){
        $connectionParams = [
            'dbname' => config('database.connections.mysql.database'),
            'user' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
            'host' => config('database.connections.mysql.host'),
            'driver' => 'pdo_mysql',
        ];

        $this->module = $appModuleName;
        $this->moduleLower = Str::lower($appModuleName);
        $this->table = $tableName;
        $this->tableUpper = Str::of($tableName)->camel()->ucfirst()->toString();
        $this->connection = DriverManager::getConnection($connectionParams);
        $this->stubPath = base_path(config('tomato-flutter.stubs-path')) . "/";
        $this->cols = $this->getCols();
    }

    /**
     * @return void
     */
    public function generate(): void
    {
        $this->generateModel();
        $this->generateServices();
        $this->generateControllers();
        $this->generatePages();
        $this->generateRoutes();
        $this->generateModule();
    }

}
