<?php
return ['default'=>env('CACHE_STORE','database'),'stores'=>['array'=>['driver'=>'array'],'database'=>['driver'=>'database','table'=>'cache'],'file'=>['driver'=>'file','path'=>storage_path('framework/cache/data')]],'prefix'=>'employee_management_cache'];
