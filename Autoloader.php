<?php

/**
 * Simple PSR-4 Autoloader
 */
class Autoloader
{
    private static array $directories = [];

    /**
     * Register base directory for your classes
     * @param  string  $baseNamespace  Base namespace (e.g., 'App')
     * @param  string  $directory  Directory path where classes are located
     */
    public static function register(string $baseNamespace, string $directory): void
    {
        // Normalize directory path
        $directory = rtrim($directory, '/\\').DIRECTORY_SEPARATOR;

        // Store in static property
        self::$directories[$baseNamespace] = $directory;

        // Register autoloader if not already registered
        if (!spl_autoload_functions() || !in_array([self::class, 'autoload'], spl_autoload_functions())) {
            spl_autoload_register([self::class, 'autoload']);
        }
    }

    /**
     * PSR-4 autoloader
     * @param  string  $className  Fully qualified class name
     */
    public static function autoload(string $className): void
    {
        // Try each registered directory
        foreach (self::$directories as $baseNamespace => $directory) {
            // Check if class belongs to this namespace
            if (str_starts_with($className, $baseNamespace)) {
                // Remove base namespace and leading backslash
                $className = substr($className, strlen($baseNamespace));
                $className = ltrim($className, '\\');

                // Convert namespace separators to directory separators
                $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);

                // Build full file path
                $file = $directory.$path.'.php';

                // Include file if it exists
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    }
}
