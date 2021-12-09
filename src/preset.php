<?php
/**
 * App Preset
 *
 * This file is imported when an app instance is constructed and
 * defines the preconfig values for the application instance.
 *
 * @package Baobab
 * @author  Joshua Sello <joshuasello@gmail.com>
 * @since   1.0.0
 */


return [
    "app" => [

        "dirs" => [
            "assets" => "assets/",
            "middlerware_extensions" => "extensions/Middlleware/",
            "providers_extensions" => "extensions/Providers/"
        ],

        // time configs
        "default_timezone" => "Africa/Johannesburg",
        "date_format" => "Y-m-d",
        "time_format" => "H:i:s"

    ],

    "templating" => [
        "engine" => null,
        "templates_path" => "templates/",
        "compiled_path" => "compiled/"
    ],

    "asset_preprocessor" => [
        "sass" => [
            "src" => "assets/scss",
            "dst" => "assets/css"
        ]
    ]
];
