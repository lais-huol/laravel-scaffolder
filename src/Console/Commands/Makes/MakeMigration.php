<?php

namespace LAIS\Scaffold\Console\Commands\Makes;


use LAIS\Scaffold\Console\Commands\Scaffolding;
use LAIS\Scaffold\Console\Commands\Migration\CreateSchema;
use Illuminate\Filesystem\Filesystem;

class MakeMigration
{
    protected $scaffolding;
    protected $files;

    private $tableName;

    public function __construct(Scaffolding $scaffolding, Filesystem $files)
    {
        $this->scaffolding = $scaffolding;
        $this->files = $files;

        $this->start();
    }


    protected function start()
    {
        //The name of the migration file
        $date = date('Y_m_d_His');
        $filename = $date . '_create_' . mb_strtolower($this->scaffolding->plural) . '_table';

        $path = './database/migrations/' . $filename . '.php';

        //Check if exists a file with the same name
        if($this->files->exists($path))
        {
            return $this->scaffolding->error('O arquivo de migração ' . $filename . ' já existe');
        }

        //Execute
        $this->files->put($path, $this->createMigration());
        $this->scaffolding->info('Migração criada com sucesso');
    }

    protected function createMigration()
    {
        $stub = $this->files->get(dirname(__DIR__) . '/stubs/migration.stub');

        $this->replaceClassName($stub)->replaceSchema($stub);
        return $stub;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceClassName(&$stub)
    {
        $className = ucwords(camel_case('Create' . $this->scaffolding->plural . 'Table'));
        $stub = str_replace('{{class}}', $className, $stub);

        return $this;
    }

    /**
     * Replace the table name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceTableName(&$stub)
    {
        $this->tableName = mb_strtolower($this->scaffolding->plural);
        $stub = str_replace('{{table}}', $this->tableName, $stub);
        return $this;
    }

    /**
     * Replace the schema for the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceSchema(&$stub)
    {
        if($schema = $this->scaffolding->getSchema())
        {
            $this->tableName = str_plural(snake_case($this->scaffolding->getModelName()));
            $schema = (new CreateSchema)->parse($this->tableName, $schema);
            $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);
        }

        return $this;
    }

}
