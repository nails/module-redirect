<?php

use Nails\Redirect\Model\Redirect;

return [
    'models' => [
        'Redirect' => function (): Redirect {
            if (class_exists('\App\Redirect\Model\Redirect')) {
                return new \App\Redirect\Model\Redirect();
            } else {
                return new Redirect();
            }
        },
    ],
];
