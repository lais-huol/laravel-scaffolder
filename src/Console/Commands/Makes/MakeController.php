<?php
/**
 * Created by PhpStorm.
 * User: sorriso
 * Date: 01/09/16
 * Time: 16:32
 */

namespace LAIS\Scaffold\Console\Commands\Makes;

use LAIS\Scaffold\Console\Commands\Scaffolding;
use Illuminate\Filesystem\Filesystem;
use LAIS\Scaffold\Console\Commands\Migration\CreateSchema;

class MakeController
{
    protected $scaffolding;
    protected $files;

    private $className;

    public function __construct(Scaffolding $scaffolding, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffolding = $scaffolding;

        $this->start();
    }

    protected function createCrudController()
    {
        $path = './app/Http/Controllers/CrudController.php';
        if(!$this->files->exists($path))
        {
            $stub = $this->files->get(dirname(__DIR__) . '/stubs/crudcontroller.stub');
            $this->replaceAppNamespace($stub);
            $this->files->put($path, $stub);
        }
    }

    protected function start()
    {
        $this->className = $this->scaffolding->getModelName();

        $path = './app/Http/Controllers/' . $this->className . 'Controller.php';

        //Check if exists a file with the same name
        if($this->files->exists($path))
        {
            return $this->scaffolding->error('The controller file ' . $this->className . ' already exists');
        }

        //Execute
        $this->files->put($path, $this->createController());
        $this->createCrudController();

        $this->scaffolding->info('Controller created successfully');

    }

    protected function createController()
    {
        $stub = $this->files->get(dirname(__DIR__) . '/stubs/controller.stub');

        $this->replaceAppNamespace($stub)->replaceClassName($stub)->replaceModelName($stub)->replaceValidateRulesCreate($stub);
        return $stub;
    }

    protected function replaceAppNamespace(&$stub)
    {
        $stub = str_replace('{{appNamespace}}', \App::getNamespace(), $stub);

        return $this;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceClassName(&$stub)
    {
        $stub = str_replace('{{class}}', $this->className . 'Controller', $stub);

        return $this;
    }


    /**
     * Replace the schema for the stub.
     *
     * @param  string $stub
     * @param string $type
     * @return $this
     */
    protected function replaceModelName(&$stub)
    {
        $stub = str_replace('{{model}}', $this->className, $stub);
        return $this;
    }

    protected function getFields($schema){
            $fields = [];
            $schemas = (new CreateSchema)->getFields($schema);
            foreach ($schemas as $sch)
            {
              $fields[] = "'" . $sch->name . "'";
            }

            return $fields;
    }

    protected function replaceValidateRulesCreate(&$stub)
    {
        $fields = $this->getFields($this->scaffolding->getSchema());
        $rules = '';
        $rules .= <<<STRING
\t\t\$rules = [\n
STRING;

        foreach($fields as $field)
        {
            $rules .= "\t\t\t{$field} => 'required', \n";
        }

        $rules .= <<<STRING
\t\t];
\t\treturn Validator::make(\$request->all(), \$rules);
STRING;

        $stub = str_replace('{{validateRules}}', $rules, $stub);

        return $this;
    }
}
