# Dougl/Datalayer
O datalayer é um componente baseado nos padrões MVC utilizando a biblioteca PDO com prepared statements. Foi desenvolvido para facilitar a manipulação de dados em operações simples como ler, inserir, editar e excluir.

## Instalação
Sua instalação pode ser feita através do composer com o seguinte comando:
```
composer require dougl/datalayer
```

## Documentação
### Conexão
Para começar precisamos configurar a conexão com o banco de dados. Subistitua os dados conforme a sua necessidade.
```php
define(DB_CONFIG, [
  "driver"    => "mysql",
  "host"      => "localhost",
  "db_name"   => "datalayer",
  "db_user"   => "root",
  "db_passwd" => ""
]);
```
### Ler
Para lermos os dados podemos seguir o exemplo:
```php
<?php
use Dougl\DataLayer;
$model = new DataLayer("user");

// Buscar todos os usuários
$users = $model->read("SELECT * FROM %s");
// ou $users = $model->findAll();

foreach($users as $user) {
  echo $user->name . "<br>";
}

// Buscar um usuário pelo ID
$user = $model->read("SELECT * FROM %s WHERE id=?", [12], false);
// ou $model->findByPrimaryKey(12);

echo $user->name;
```
