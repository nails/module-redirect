<?php

return array(
    'models' => array(
        'Redirect' => function () {
            if (class_exists('\App\Redirect\Model\Redirect')) {
                return new \App\Redirect\Model\Redirect();
            } else {
                return new \Nails\Redirect\Model\Redirect();
            }
        }
    )
);
