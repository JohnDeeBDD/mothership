<?php
/**
 * Environment and Operating System related functions.
 *
 * @package tad\WPBrowser
 */

namespace tad\WPBrowser;

use tad\WPBrowser\Utils\Map;

/**
 * Returns a closure to get the value of an environment variable, loading a specific env file first.
 *
 * @param string $file The name of the environment file to load.
 *
 * @return Map The map of the read environment variables.
 *
 * @throws \RuntimeException If the env file does not exist or is not readable.
 */
function envFile($file)
{
    if (!(file_exists($file))) {
        throw new \InvalidArgumentException('File ' . $file . ' does not exist.');
    }
    if (!is_readable($file)) {
        throw new \InvalidArgumentException('File ' . $file . ' is not readable.');
    }

    $envFileContents = file_get_contents($file);

    if ($envFileContents === false) {
        throw new \InvalidArgumentException('Could not read ' . $file . ' contents.');
    }

    $vars = array_reduce(array_filter(explode(PHP_EOL, $envFileContents)), static function (array $lines, $line) {
        if (preg_match('/^\\s*#/', $line)) {
            return $lines;
        }

        list($key, $value) = explode('=', $line);
        $lines[$key] = $value;
        return $lines;
    }, []);

    return new Map($vars);
}

/**
 * Returns the pretty, human-readable name of the current Operating System.
 *
 * @return string The human-readable name of the OS PHP is running on. One of `Windows`, `Linux`, 'macOS`, 'Solaris`,
 *                `BSD` or`Unknown`.
 */
function os()
{
    $constant = defined(PHP_OS_FAMILY) ? 'PHP_OS_FAMILY' : 'PHP_OS';
    $osSlug = strtolower(substr(constant($constant), 0, 3));

    $map = [
        'win' => 'Windows',
        'lin' => 'Linux',
        'dar' => 'macOS',
        'bsd' => 'BSD',
        'sol' => 'Solaris'
    ];

    return isset($map[$osSlug]) ? $map[$osSlug] : 'Unknown';
}

/**
 * Loads a Map of environment variables into `getenv()`, `$_ENV` and `$_SERVER`.
 *
 * @param Map $map The map of environment variables to load.
 *
 * @see envFile() For the function to load to generate a Map from an environment file.
 */
function loadEnvMap(Map $map)
{
    if (empty($_SERVER)) {
        $_SERVER = [];
    }

    if (empty($_ENV)) {
        $_ENV = [];
    }

    foreach ($map->toArray() as $key => $value) {
        putenv("{$key}={$value}");
        $_SERVER[$key] = $value;
        $_ENV[$key] = $value;
    }
}
