<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'material-icons' => [
        'version' => '1.13.12',
    ],
    'material-icons/iconfont/material-icons.min.css' => [
        'version' => '1.13.12',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'chart.js' => [
        'version' => '3.9.1',
    ],
    'svelte/internal' => [
        'version' => '3.59.2',
    ],
    'svelte/transition' => [
        'version' => '3.59.2',
    ],
    'svelte' => [
        'version' => '3.59.2',
    ],
    'svelte/store' => [
        'version' => '3.59.2',
    ],
];
