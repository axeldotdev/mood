<?php

arch()->preset()->php();

arch()->preset()->security();

arch()->preset()->laravel();

arch('app')
    ->expect('App')
    ->classes()
    ->toBeFinal();

arch('enums')
    ->expect('App\Enums')
    ->toBeEnums();
