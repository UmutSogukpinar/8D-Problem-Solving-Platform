<?php
declare(strict_types=1);



require_once dirname(__DIR__) . '/config/constants.php';

/**
 * Writes a log message to the application log file.
 *
 * @param string $level Log level prefix (INFO, WARNING, ERROR)
 * @param string $message Log message
 *
 * @return void
 */
function logMessage(string $level, string $message): void
{
    if (!shouldLog($level))
        return ; 
    
    if (!is_dir(LOG_DIR))
        mkdir(LOG_DIR, 0755, true);
    
    $time = date('Y-m-d H:i:s');
    $script = $_SERVER['SCRIPT_NAME'] ?? 'cli';

    $line = sprintf(
        "[%s] %s%s (%s)%s",
        $time,
        $level,
        $message,
        $script,
        PHP_EOL
    );

    file_put_contents(
        APP_LOG,
        $line,
        FILE_APPEND | LOCK_EX
    );
}

function initLogger(): void
{
    if (!is_dir(LOG_DIR))
    {
        mkdir(LOG_DIR, 0755, true);
    }

    // truncate log file
    file_put_contents(APP_LOG, '');
}

/**
 * Determines whether a log level should be written.
 *
 * @param string $level
 *
 * @return bool
 */
function shouldLog(string $level): bool
{
    $levels = [
        DEBUG => 1,
        INFO => 2,
        WARNING => 3,
        ERROR => 4
    ];

    return ($levels[$level] >= $levels[LOG_LEVEL]);
}
