<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

date_default_timezone_set('Europe/Madrid'); // aplica la zona horaria de españa

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
