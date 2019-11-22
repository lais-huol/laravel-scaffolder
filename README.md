# CRUD Scaffold para Laravel 5.3
Este pacote auxilia a criar CRUD's

```sh
$ php artisan make:scaffold Cliente --schema="nome:string, telefone:string, data_nascimento:date" --plural="Clientes" --singular="Cliente"
$ php artisan make:scaffold Instituicao --schema="nome:string('200'):default('LAIS'), cnpj:string:nullable, quantidade_funcionarios:integer" -p Clientes -s Cliente
```

### Sumário
 - [O que ele faz](#o-que-ele-faz)
 - [Requerimentos](#requerimentos)
 - [Instalação](#instalação)
 - [Como usar](#como-usar)

### O que ele faz
Ele cria um CRUD já com todos os métodos [--resources](https://laravel.com/docs/5.3/controllers#resource-controllers) implementados
*inclui*:
 - views: index, show, create, edit
 - CrudController (com os métodos implementados)
 - (My)Controller, extendendo o CrudController
 - migração seguindo a definicao do parâmetro --schema do comando
 - Adição das rotas no arquivo routes/web.php
```php
Route::resource('my', 'MyController');
```


### Requerimentos
 - PHP 5.6+
 - [Laravel 5.3](https://laravel.com/docs/5.3)

### Instalação

**Composer**
Pelo terminal execute os seguintes comandos:
```sh
$ composer require lais/scaffold
```
**Registrando o comando no Laravel**
Abra o arquivo `app/Console/Kernel.php` e adicione a seguinte classe ao array `$commands`:
```php
protected $commands = [
    \LAIS\Scaffold\Console\Commands\Scaffolding::class,
];
```

**Registrando o provider no Laravel**
Abra o arquivo config/app.php e adicione a seguinte linha no array da opção `providers`:
```php
\LAIS\Scaffold\Providers\LaisServiceProvider::class,
```

### Como usar
**Comando artisan**
```sh
$ php artisan make:scaffold Cliente --schema="coluna1:tipo1, coluna2:tipo2..." --plural="Clientes" --singular="Cliente"
$ php artisan make:scaffold Empresa --schema="coluna1:tipo1:modificador1, coluna2:tipo2..." -p Clientes -s Cliente
```


A passagem de parâmetros segue o mesmo padrão das [migrações](https://laravel.com/docs/5.3/migrations#columns) do [Laravel 5.3](https://laravel.com/docs/5.3)
