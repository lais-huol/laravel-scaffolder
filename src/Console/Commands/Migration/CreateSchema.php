<?php

namespace LAIS\Scaffold\Console\Commands\Migration;

class CreateSchema
{

    private $schema = [];

    public function parse($tableName, $schema) {
        $this->schema[] = $this->schemaUp($tableName, $schema);
        $this->schema[] = $this->schemaDown($tableName);

        return $this->schema;
    }

    protected function schemaUp($tableName, $schema) {
        $schemaUp = "Schema::create('".$tableName."', function (Blueprint \$table) {\n";
        $schemaUp .= $this->createColumns($this->getFields($schema));
        $schemaUp .= "\t\t});";

        return $schemaUp;
    }

    protected function schemaDown($tableName) {
        return "Schema::drop('".$tableName."');";
    }

    protected function createColumns($fields) {
        $columns = "\t\t\t\$table->increments('id');\n";
        
        foreach ($fields as $field) {
            $columns .= "\t\t\t\$table->".$field->type."('".$field->name."');\n";
        }

        $columns .= "\t\t\t\$table->timestamps();\n";

        return $columns;
    }

    protected function getFields($schema) {
        $schemas = explode(",", $schema);
        $fields = array();

        foreach ($schemas as $schema) {
            $parts = explode(":", $schema);
            $field = new \stdClass();
            $field->name = trim($parts[0]);
            $field->type = trim($parts[1]);
            $fields[] = $field;
        }

        return $fields;
    }

}


//--schema="title:string:default('Tweet #1'), body:text"

/*
 *      Schema::create('flights', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('airline');
            $table->timestamps();
        });
 */