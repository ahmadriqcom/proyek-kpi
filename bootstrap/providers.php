<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
];
