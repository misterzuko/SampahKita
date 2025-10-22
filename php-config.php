<?php
//API ERROR HANDLING
set_error_handler(function ($severity, $message, $file, $line) {
    $errorData = [
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line
    ];

    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'warning',
        'error' => $errorData
    ]);

    exit; 
});


//FATAL ERROR
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'status' => 'fatal_error',
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        exit;
    }
});
?>