# SQL Helpers

## Descripción

`sql-helpers` es una librería PHP que proporciona funciones útiles para manejar conexion a una base de datos MySQL o SQL server para ejecutar procedimientos almacenados que insertan datos, consultas en procedimientos almacenados y consulta de funciones escalares y funciones de tabla de SQL server asi como el query directo de select, update, etc. Basado en el modelo de Laravel 'DB'.

# Instalación

Para instalar `sql-helpers`, puedes usar Composer. Ejecuta el siguiente comando en tu terminal:

```bash
composer require gabogalro/sql-helpers
```

# Configuracion global

## Descripción

la libreria `sql-helpers` crea una instancia de conexión a base datos únicamente especificando el tipo de driver en `DB_DRIVER=` como `mysql` o `sqlsrv` permitiendo de esta manera una conexion unica y rapida que permite ejecutar los distintos tipos de metodos para manejar la base de datos de forma directa, sin el uso de un ORM.

## MySQL

```env
DB_DRIVER=mysql
DB_DATABASE=tuBaseDeDatos
DB_USERNAME=TuUsuario
DB_PASSWORD=TuContraseña
DB_CHARSET=utf8mb4
DB_SERVER=localhost # -> la mayoria de instancias de mysql son localhost, verifica en tu servidor
```

## SQL Server

```env
#conexion para sqlsrv
DB_DRIVER=sqlsrv
DB_DATABASE=tuBaseDeDatos
DB_USERNAME=TuUsuario
DB_PASSWORD=TuContraseña
DB_CHARSET=utf8mb4
DB_SERVER=tuInstanciaSqlServer # -> todas las instancias de sql server son diferentes, verifica en tu servidor
```

| Variable    | Descripción                             | Ejemplo                    |
| ----------- | --------------------------------------- | -------------------------- |
| DB_DRIVER   | Driver de conexión (`mysql` o `sqlsrv`) | `mysql`                    |
| DB_DATABASE | Nombre de la base de datos              | `mi_proyecto`              |
| DB_USERNAME | Usuario de la BD                        | `root`                     |
| DB_PASSWORD | Contraseña del usuario de la BD         | `secret`                   |
| DB_CHARSET  | Codificación (solo aplica a MySQL)      | `utf8mb4`                  |
| DB_SERVER   | Host o instancia del servidor de la BD  | `localhost` / `SQLEXPRESS` |

# Usos para statement

## El metodo statement sirve para ejecutar procedimientos almacenados que insertan datos, este metodo debe recibir los parametros en formato de array.

```php
<?php

use gabogalro\SQLHelpers\DB;

public function insertData(){

  $data = [
    'nombre' => 'juan',
    'apellido' => 'perez'
  ];

  $array = array_values($data);

  //ejemplo de uso para sql server con $data en formato array
  DB::statement('exec sp_insert ?, ?', $array);

  //ejemplo de uso para sql server enviando cada parametro individual
  DB::statement('exec sp_insert ?, ?', [$data['nombre'], $data['apellido']]);

  //ejemplo de uso para mysql
  DB::statement('call sp_insert (?, ?)', $array);

  //ejemplo de uso para mysql con parametros individuales
  DB::statement('call sp_insert (?, ?)', [$data['nombre'], $data['apellido']]);

}
```

# Usos para selectOne

## el metodo selectOne sirve para ejecutar consultas que filtran por un dato especifico, como un id o un dato unico y regresa un dato unico

### funciona para procedimientos almacenados, funciones escalares y querys directos; en el caso de mysql unicamente para procedimientos almacenados y query directo.

```php

use gabogalro\SQLHelpers\DB;

//sql server y mysql
public function getById(){
     $paramId = 1;

     $getEmpleado = DB::selectOne('select nombre from empleados where id = ?', [$paramId]);
}

//sql server
public function getById(){
     $paramId = 1;

     $getEmpleado = DB::selectOne('exec sp_get_by_id ?', [$paramId]);
}

//sql server
public function getByPayment(){
     $paramId = 1;

     $getEmpleado = DB::selectOne('select dbo.fn_tabla_empleados_paga(?)', [$paramId]);
}

//mysql
public function getById(){
     $paramId = 1;

     $getEmpleado = DB::selectOne('call sp_get_by_id(?)', [$paramId]);
}

//mysql
public function getByPayment(){
     $paramId = 1;

     $getEmpleado = DB::selectOne('select fn_tabla_empleados_paga(?)', [$paramId]);
}
```

# Usos para selectAll

## el metodo selectAll sirve para ejecutar consultas que filtran por uno o mas datos y retornan un array de N cantidad de resultados

### funciona para procedimientos almacenados, funciones de tabla y querys directos; en el caso de mysql unicamente para procedimientos almacenados y query directo.

```php

use gabogalro\SQLHelpers\DB;

//sql server y mysql
public function getEmployees(){
     $maxSalary = 5000;
     $minSalary = 1000;
     $getEmpleado = DB::selectAll('select nombre from empleados where salary > ? and salary < ?', [$minSalary, $maxSalary]);
}
//sp en sql server
public function getEmployees(){
     $maxSalary = 5000;
     $minSalary = 1000;
     $getEmpleado = DB::selectAll('exec sp_empleados_por_salario ?, ?', [$minSalary, $maxSalary]);
}
//sp en mysql
public function getEmployees(){
     $maxSalary = 5000;
     $minSalary = 1000;
     $getEmpleado = DB::selectAll('call sp_empleados_por_salario(?, ?)', [$minSalary, $maxSalary]);
}
```

# Uso para query

## el metodo query sirve para ejecutar consultas sin parametros, ya sea un query directo o una vista, un sp o funcion

```php
use gabogalro\SQLHelpers\DB;

public function viewEmployees(){

  //ejemplo ejecutando una vista
  $empleados = DB::query('select * from vw_empleados');

  //ejemplo ejecutando un query directo a una tabla
  $empleados = DB::query('select * from empleados');

}


```

## Requisitos previos

- PHP 7.4 o superior
- Composer

## License

MIT © gabogalro. See [LICENSE](LICENSE) for details.

```

```
