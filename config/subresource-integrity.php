<?php

return [
    'base_path' => base_path('public'),

    'algorithm' => env('SRI_ALGORITHM', 'sha256'),

    'mix_sri_path' => public_path('mix-sri.json'),

    'hashes' => [
        //
    ],
];
