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
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'material-icons' => [
        'version' => '1.13.14',
    ],
    'material-icons/iconfont/material-icons.min.css' => [
        'version' => '1.13.14',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'chart.js' => [
        'version' => '4.4.8',
    ],
    'svelte/internal' => [
        'version' => '5.22.6',
    ],
    'svelte/transition' => [
        'version' => '5.22.6',
    ],
    'svelte' => [
        'version' => '5.22.6',
    ],
    'svelte/store' => [
        'version' => '5.22.6',
    ],
    '@kurkle/color' => [
        'version' => '0.3.4',
    ],
    'esm-env' => [
        'version' => '1.2.2',
    ],
    'clsx' => [
        'version' => '2.1.1',
    ],
    'esm-env/browser' => [
        'version' => '1.2.2',
    ],
    'esm-env/development' => [
        'version' => '1.2.2',
    ],
    'esm-env/node' => [
        'version' => '1.2.2',
    ],
    'chart.js/auto' => [
        'version' => '4.4.8',
    ],
];
