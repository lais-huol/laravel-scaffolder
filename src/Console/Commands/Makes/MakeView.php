<?php

namespace LAIS\Scaffold\Console\Commands\Makes;

use LAIS\Scaffold\Console\Commands\Scaffolding;
use Illuminate\Filesystem\Filesystem;
use LAIS\Scaffold\Console\Commands\Migration\CreateSchema;

class MakeView
{

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = ['create', 'edit', 'index', 'show'];

    private $scaffolding;
    private $folder;

    public function __construct(Scaffolding $scaffolding, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffolding = $scaffolding;
        $this->start();
    }

    private function start()
    {
        $this->folder = str_plural(snake_case($this->scaffolding->getModelName()));
        $this->createDirectories();
        $this->createAppView();
        $this->generateView();
    }

    protected function generateView()
    {
        $this->scaffolding->info('Criando views...');
        foreach($this->views as $view)
        {
            // Get path
            $path = './resources/views/' . $this->folder . '/' . $view . '.blade.php';
            //check if path exists
            if($this->files->exists($path))
            {
                if($this->scaffolding->confirm($path . ' já existe! Deseja sobrescrever? [yes|no]'))
                {
                    switch($view)
                    {
                        case 'index':
                            $this->generateIndex($path);
                            break;
                        case 'create':
                            $this->generateCreate($path);
                            break;
                        case 'show':
                            $this->generateShow($path);
                            break;
                        case 'edit':
                            $this->generateEdit($path);
                            break;
                    }
                }
            }
            else
            {
                switch($view)
                {
                    case 'index':
                        $this->generateIndex($path);
                        break;
                    case 'create':
                        $this->generateCreate($path);
                        break;
                    case 'show':
                        $this->generateShow($path);
                        break;
                    case 'edit':
                        $this->generateEdit($path);
                        break;
                }
            }
        }
    }

    protected function generateIndex($path = '')
    {
        $schema = $this->scaffolding->getSchema();

        $stub = $this->files->get(dirname(__DIR__) . '/stubs/views/index.stub');

        //$stub = $this->files->get($path);

        //Change the name of the class
        $stub = str_replace('{{class}}', $this->folder, $stub);

        //Change the single name
        $single = strtolower($this->scaffolding->getModelName());
        $stub = str_replace('{{single}}', $single, $stub);
        $stub = str_replace('{{plural}}', $this->scaffolding->plural, $stub);
        $stub = str_replace('{{singular}}', $this->scaffolding->singular, $stub);

        //Complete the {{tableheader}} and the {{content_fields}}
        $fields = $this->getFields($schema);
        $tableHeader = "";
        $contentFields = "";
        foreach($fields as $field)
        {
            $tableHeader .= "\t\t\t\t\t\t\t\t\t<th>" . ucfirst($field) . "</th>\n";
            $contentFields .= "\n\t\t\t\t\t\t\t\t\t\t<td>{{ \$item->" . $field . " }}</td>";
        }
        $stub = str_replace('{{tableheader}}', $tableHeader, $stub);
        $stub = str_replace('{{content_fields}}', $contentFields, $stub);

        // Put file
        $this->files->put($path, $stub);
        $this->scaffolding->info('View index criada com sucesso');
    }

    protected function generateCreate($path = '')
    {
        $schema = $this->scaffolding->getSchema();

        $stub = $this->files->get(dirname(__DIR__) . '/stubs/views/create.stub');

        //Change the name of the class
        $stub = str_replace('{{class}}', $this->folder, $stub);


        //Complete the {{formFields
        $fields = $this->getFields($schema);
        $formFields = [];
        foreach($fields as $field)
        {
            $uc = ucfirst($field);
            $formFields[] = <<<STRING
\t\t\t\t\t\t\t<div class="form-group">
\t\t\t\t\t\t\t\t<label for="{$field}">{$uc}</label>
\t\t\t\t\t\t\t\t<input type="text" name="{$field}" class="form-control" id="{$field}" placeholder="{$field}" value="{{ old('{$field}') }}">
\t\t\t\t\t\t\t</div>
STRING;
        }
        $stub = str_replace('{{formFields}}', implode("\n", $formFields), $stub);
        $stub = str_replace('{{plural}}', $this->scaffolding->plural, $stub);
        $stub = str_replace('{{singular}}', $this->scaffolding->singular, $stub);
        // Put file
        $this->files->put($path, $stub);
        $this->scaffolding->info('View create criada com sucesso');
    }

    protected function generateEdit($path = '')
    {
        $schema = $this->scaffolding->getSchema();

        $stub = $this->files->get(dirname(__DIR__) . '/stubs/views/edit.stub');

        //Change the name of the class
        $stub = str_replace('{{class}}', $this->folder, $stub);
        $stub = str_replace('{{singular}}', $this->scaffolding->singular, $stub);


        //Complete the {{formFields
        $fields = $this->getFields($schema);
        $formFields = [];
        foreach($fields as $field)
        {
            $uc = ucfirst($field);
            $formFields[] = <<<STRING
\t\t\t\t\t\t\t<div class="form-group">
\t\t\t\t\t\t\t\t<label for="{$field}">{$uc}</label>
\t\t\t\t\t\t\t\t<input type="text" name="{$field}" value="{{ \$item->{$field} }}" class="form-control" id="{$field}" placeholder="{$field}">
\t\t\t\t\t\t\t</div>
STRING;
        }
        $stub = str_replace('{{formFields}}', implode("\n", $formFields), $stub);
        // Put file
        $this->files->put($path, $stub);
        $this->scaffolding->info('View edit criada com sucesso');
    }

    protected function generateShow($path = '')
    {
        $schema = $this->scaffolding->getSchema();

        $stub = $this->files->get(dirname(__DIR__) . '/stubs/views/show.stub');

        //Change the name of the class
        $stub = str_replace('{{class}}', $this->folder, $stub);
        $stub = str_replace('{{modelName}}', $this->scaffolding->getModelName(), $stub);
        $stub = str_replace('{{singular}}', $this->scaffolding->singular, $stub);


        //Complete the {{formFields
        $fields = $this->getFields($schema);
        $formFields = [];
        foreach($fields as $field)
        {
            $uc = ucfirst($field);
            $formFields[] = <<<STRING
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th>{$uc}</th>
\t\t\t\t\t\t\t\t<td>{{ \$item->{$field} }}</td>
\t\t\t\t\t\t\t</tr>
STRING;
        }
        $stub = str_replace('{{tableRows}}', implode("\n", $formFields), $stub);
        // Put file
        $this->files->put($path, $stub);
        $this->scaffolding->info('View show criada com sucesso');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if(!is_dir(base_path('resources/views/layouts')))
        {
            mkdir(base_path('resources/views/layouts'), 0755, true);
        }

        if(!is_dir(base_path('resources/views/' . $this->folder)))
        {
            mkdir(base_path('resources/views/' . $this->folder), 0755, true);
        }
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createAppView()
    {
        copy(dirname(__DIR__) . '/stubs/views/layouts/app.stub', base_path('resources/views/layouts/app.blade.php'));
    }

    protected function getFields($schema)
    {
        $fields = [];
        $schemas = (new CreateSchema)->getFields($schema);
        foreach ($schemas as $sch)
        {
          $fields[] = $sch->name;
        }

        return $fields;
    }
}
