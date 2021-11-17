<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Run these command in bash/cmd
- clone : `https://github.com/mehadiHsn/docManagementSystem.git` then `cd docmanagement`
- `composer install` and `npm install`
- rename the **.env.example** to **.env** then add database config
- `php artisan storage:link`
- `php artisan key:generate`
- `php artisan migrate`

## For admin
- localhost:8000/admin

## Note
- To get the latest changes in you local machine.
- Download this git repo as zip
- copy the all the files and replace with the old folder
- no need to run `composer install` and `npm install`

## custom nav style added to app.scss (remove them to get the default style)

```scss
li.custom-nav:hover,
li.custom-nav:focus{
  background: rgba(89, 241, 252,0.5);
}
li.custom-nav > a.router-link-exact-active{
  background: rgba(57, 206, 239,1);
  color:white;
  text-decoration: none;
}
.navbar-laravel {
  background-color: rgba(0,0,0,0.1);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}
```
