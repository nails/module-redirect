<?php

use Nails\Redirect\Model;
use Nails\Redirect\Resource;
use Nails\Redirect\Service;

return [
    'services'  => [
        'Redirect' => function (): Service\Redirect {
            if (class_exists('\App\Redirect\Service\Redirect')) {
                return new \App\Redirect\Service\Redirect();
            } else {
                return new Service\Redirect();
            }
        },
    ],
    'models'    => [
        'Redirect' => function (): Model\Redirect {
            if (class_exists('\App\Redirect\Model\Redirect')) {
                return new \App\Redirect\Model\Redirect();
            } else {
                return new Model\Redirect();
            }
        },
    ],
    'resources' => [
        'Redirect' => function ($resource, $model): Resource\Redirect {
            if (class_exists('\App\Redirect\Resource\Redirect')) {
                return new \App\Redirect\Resource\Redirect($resource, $model);
            } else {
                return new Resource\Redirect($resource, $model);
            }
        },
    ],
];
