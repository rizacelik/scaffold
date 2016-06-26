# Laravel 5* Scaffold Generator

Laravel5* Scaffold All (Model, Controller, views) Generator


## Usage

### Step 1: Install Through Composer

```
composer require scaffold/builder: "dev-master"
```

### Step 2: Add the Service Provider


Open `config/app.php` and, add to `Crudbuilder\Builder\GeneratorsServiceProvider::class`

```
'providers' => [
     . . .
     Scaffold\Builder\GeneratorsServiceProvider::class,
],
```


### Step 3: Run Artisan!

~~~
php artisan make:scaffold
~~~



## Examples


```
C:\xampp\htdocs\laravel>php artisan make:scaffold

 Generate all controller, model and views? [y|N] (yes/no) [no]:
 > y

==================================================

 Notice: Are you backed up if exists files? [y|N] (yes/no) [no]:
 > n
```

C:\xampp\htdocs\laravel52\resources\views/posts\create.blade.php
C:\xampp\htdocs\laravel52\resources\views/posts\edit.blade.php
C:\xampp\htdocs\laravel52\resources\views/posts\show.blade.php
C:\xampp\htdocs\laravel52\resources\views/posts\index.blade.php
C:\xampp\htdocs\laravel52\resources\views/posts\_form.blade.php
C:\xampp\htdocs\laravel52\app\Http\Controllers\PostsController.php
C:\xampp\htdocs\laravel52\app\Posts.php
C:\xampp\htdocs\laravel52\resources\views/categories\create.blade.php
C:\xampp\htdocs\laravel52\resources\views/categories\edit.blade.php
C:\xampp\htdocs\laravel52\resources\views/categories\show.blade.php
C:\xampp\htdocs\laravel52\resources\views/categories\index.blade.php
C:\xampp\htdocs\laravel52\resources\views/categories\_form.blade.php
C:\xampp\htdocs\laravel52\app\Http\Controllers\CategoriesController.php
C:\xampp\htdocs\laravel52\app\Categories.php
C:\xampp\htdocs\laravel52\app\Comments.php

C:\xampp\htdocs\laravel52>




## Scaffold
![image](http://i67.tinypic.com/2ujpcsz.jpg)
![image](http://i66.tinypic.com/w9b7g9.png)
![image](http://i66.tinypic.com/1zozwb8.jpg)


