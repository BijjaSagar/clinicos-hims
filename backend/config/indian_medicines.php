<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Path to medicines.json (same format as github.com/nowitsidb/INDIAN_MEDICINE_MCP_SERVER)
    |--------------------------------------------------------------------------
    |
    | The MCP server loads this file locally; there is no public HTTP API.
    | Obtain the JSON from the dataset author or export your own, then:
    | php artisan indian-medicines:import /full/path/to/medicines.json
    |
    */
    'json_path' => env('INDIAN_MEDICINES_JSON_PATH', ''),

];
