<?php

return [
    'data_root' => env('HERMES_DATA_ROOT', env('HOME') . '/.hermes'),
    'log_tail'  => (int) env('HERMES_LOG_TAIL', 30),
    'gateway_stale_after' => (int) env('HERMES_GATEWAY_STALE_SECONDS', 30),
];
