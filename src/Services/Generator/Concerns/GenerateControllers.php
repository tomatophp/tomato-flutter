<?php

namespace TomatoPHP\TomatoFlutter\Services\Generator\Concerns;

use Illuminate\Support\Facades\File;

trait GenerateControllers
{
    private function generateControllers(): void
    {
        $indexControllerPath = base_path('/flutter/' . $this->appName . '/lib/app/modules/'.$this->module.'/controllers/'.$this->module.'IndexController.dart');
        $createControllerPath = base_path('/flutter/' . $this->appName . '/lib/app/modules/'.$this->module.'/controllers/'.$this->module.'CreateController.dart');
        $editControllerPath = base_path('/flutter/' . $this->appName . '/lib/app/modules/'.$this->module.'/controllers/'.$this->module.'EditController.dart');
        $viewControllerPath = base_path('/flutter/' . $this->appName . '/lib/app/modules/'.$this->module.'/controllers/'.$this->module.'ViewController.dart');

        //Delete Index Controller If Exists
        if (File::exists($indexControllerPath)) {
            File::delete($indexControllerPath);
        }

        //Delete Create Controller If Exists
        if (File::exists($createControllerPath)) {
            File::delete($createControllerPath);
        }

        //Delete Edit Controller If Exists
        if (File::exists($editControllerPath)) {
            File::delete($editControllerPath);
        }

        //Delete View Controller If Exists
        if (File::exists($viewControllerPath)) {
            File::delete($viewControllerPath);
        }

        //Create Index Controller
        $this->generateStubs(
            $this->stubPath . "/controllers/index.stub",
            $indexControllerPath,
            [
                "module" => $this->module,
                "table" => $this->table,
                "tableUpper" => $this->tableUpper,
                "moduleLower" => $this->moduleLower,
            ]
        );

        // Create Create Controller
        $this->generateStubs(
            $this->stubPath . "/controllers/create.stub",
            $createControllerPath,
            [
                "module" => $this->module,
                "table" => $this->table,
                "tableUpper" => $this->tableUpper,
                "moduleLower" => $this->moduleLower,
                "fields" => $this->controllerFields(),
                "jsonFields" => $this->controllerJsonFields()
            ]
        );
    }

    private function controllerFields(): string
    {
        $controllerFields = "  ";
        foreach($this->cols as $key=>$item){
            if($key!== 0){
                $controllerFields .= "    ";
            }
            if($item['type'] === 'int'){
                $controllerFields .= "int? ".$this->handelName($item['name']).';';
            }
//            else if($item['type'] === 'relation'){
//
//            }
            else if($item['type'] === 'boolean'){
                $controllerFields .= "bool? ".$this->handelName($item['name']).';';
            }
            else if($item['type'] === 'json' && ($item['name']== 'name' ||$item['name']== 'title'|| $item['name']== 'description')){
                $controllerFields .= "Name? ".$this->handelName($item['name']).';';
                $this->hasJson = true;
            }
            else {
                $controllerFields .= "String? ".$this->handelName($item['name']).';';
            }

            if($key!== count($this->cols)-1){
                $controllerFields .= PHP_EOL;
            }
        }
        return $controllerFields;
    }

    private function controllerJsonFields(): string
    {

    }
}
