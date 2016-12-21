<?php

namespace LAIS\Scaffold\Console\Commands\Makes;


use LAIS\Scaffold\Console\Commands\Scaffolding;
use Illuminate\Filesystem\Filesystem;
use LAIS\Scaffold\Console\Commands\Migration\CreateSchema;


class MakeModel
{
    protected $scaffolding;
    protected $files;

    private $className;
    private $useModels = '';

    public function __construct(Scaffolding $scaffolding, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffolding = $scaffolding;

        $this->start();
    }

    protected function start()
    {
        $this->className = $this->scaffolding->getModelName();

        $path = './app/' . $this->className . '.php';

        //Check if exists a file with the same name
        if($this->files->exists($path))
        {
            return $this->scaffolding->error('O model ' . $this->className . '.php já existe');
        }

        $relationships = [];
        if($this->scaffolding->confirm('Este modelo possui alguma relação?'))
        {
            $continue = 1;
            while($continue)
            {
                $relationship = $this->scaffolding->ask('Which one?
0) Cancelar
1) Um para um (hasOne)
2) Um para muitos (hasMany)
3) Muitos para muitos (manyToMany)
4) Partence a (belongsTo)
');

                if($relationship != 0)
                {
                    $model = $this->scaffolding->ask('Digite o nome do Model:');
                    $relation = new \stdClass();
                    $relation->relationship = $relationship;
                    $relation->model = $model;
                    $relationships[] = $relation;
                }

                $continue = $this->scaffolding->confirm('Mais algum?');
            }

        }

        //Execute
        $this->files->put($path, $this->createModel($relationships));

        $this->scaffolding->info('Model criado com sucesso');

    }

    protected function createModel($relationships)
    {
        $stub = $this->files->get(dirname(__DIR__) . '/stubs/model.stub');

        $this->replaceClassName($stub)->replaceTableName($stub)->replaceFields($stub);

        if(!empty($relationships))
        {
            $this->replaceRelationship($stub, $relationships);
        }
        else
        {
            $stub = str_replace('{{relationships}}', '', $stub);
        }
        $this->replaceUseModels($stub);

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
        $stub = str_replace('{{class}}', $this->className, $stub);

        return $this;
    }

    /**
     * Replace the table name in the stub.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceTableName(&$stub)
    {
        $stub = str_replace('{{tableName}}', str_plural(snake_case($this->className)), $stub);
        return $this;
    }

    protected function getFields($schema)
    {
        $fields = (new CreateSchema)->getFields($schema);
        $names = [];
        foreach ($fields as $field)
        {
            $names[] = "'" . $field->name . "'";
        }
        return implode(', ', $names);
    }


    /**
     * Replace the schema for the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceFields(&$stub)
    {
        if($schema = $this->scaffolding->getSchema())
        {
            $stub = str_replace('{{fields}}', $this->getFields($schema), $stub);
        }
        return $this;
    }

    /**
     * Replace the relationship for the stub.
     *
     * @param  string $stub
     * @param array $relationships
     * @return $this
     */
    protected function replaceRelationship(&$stub, $relationships)
    {
        $relations = '';
        foreach($relationships as $relationship)
        {
            switch($relationship->relationship)
            {
                case 1:
                    $relations .= $this->relationHasOne($relationship->model);
                    break;
                case 2:
                    $relations .= $this->relationHasMany($relationship->model);
                    break;
                case 3:
                    $relations .= $this->relationBelongsToMany($relationship->model);
                    break;
                case 4:
                    $relations .= $this->relationBelongsTo($relationship->model);
                    break;
                default:

            }
        }

        $stub = str_replace('{{relationships}}', $relations, $stub);

        return $this;
    }

    /**
     * Replace the usemodels for the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceUseModels(&$stub)
    {
        $stub = str_replace('{{usemodels}}', $this->useModels, $stub);
        return $this;
    }

    /**
     * Create the HasOne relationship.
     *
     * @param  string $model
     * @return string
     */
    protected function relationHasOne($model)
    {
        $this->useModels .= "use " . \App::getNamespace() . $model . ";\n";
        return "\tpublic function " . camel_case($model) . "()" . "\n\t{\n" . "\t\treturn \$this->hasOne(" . $model . "::class);\n" . "\t}\n\n";
    }

    /**
     * Create the HasMany relationship.
     *
     * @param  string $model
     * @return string
     */
    protected function relationHasMany($model)
    {
        $this->useModels .= "use " . \App::getNamespace() . $model . ";\n";
        return "\tpublic function " . camel_case(str_plural(snake_case($model))) . "()" . "\n\t{\n" . "\t\treturn \$this->hasMany(" . $model . "::class);\n" . "\t}\n\n";
    }

    /**
     * Create the belongsToMany relationship.
     *
     * @param  string $model
     * @return string
     */
    protected function relationBelongsToMany($model)
    {
        $this->useModels .= "use " . \App::getNamespace() . $model . ";\n";
        return "\tpublic function " . camel_case(str_plural(snake_case($model))) . "()" . "\n\t{\n" . "\t\treturn \$this->belongsToMany(" . $model . "::class);\n" . "\t}\n\n";
    }

    /**
     * Create the belongsTo relationship.
     *
     * @param  string $model
     * @return string
     */
    protected function relationBelongsTo($model)
    {
        $this->useModels .= "use " . \App::getNamespace() . $model . ";\n";
        return "\tpublic function " . camel_case($model) . "()" . "\n\t{\n" . "\t\treturn \$this->belongsTo(" . $model . "::class);\n" . "\t}\n\n";
    }
}
