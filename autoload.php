<?php declare(strict_types = 1);

namespace Psi;

if (!function_exists('Psi\loader')) {
    /**
     *  @param string $namespace Trailing \ optional.
     *  @param string $directory Trailing / optional.
     *  @param 'kebab' | 'snake' | null $naming
     *  Specify the file naming convention with the $naming parameter. The default
     *  is files named identically to the used PHP identifiers. Currently, only
     *  kebab and snake case transformations are implemented. Note that, since
     *  class names are case insensitive, the case is determined by the class name
     *  used, not the name it was defined with.
     */
    function loader (string $namespace, string $directory, ?string $naming = null):  void {
        if (!str_ends_with($namespace, '\\'))
            $namespace .= '\\';
        if (!str_ends_with($directory, '/'))
            $directory .= '/';
        spl_autoload_register(function (string $class) use ($namespace, $directory, $naming) {
            if (!str_starts_with($class, $namespace))
                return;

            $base     = $directory;
            $length   = strlen($namespace);
            $relative = substr($class, $length);
            $relative = str_replace('\\', '/', $relative);
            $relative = match ($naming) {
                'kebab' => strtolower(preg_replace('/([^A-Z\/])([A-Z])/', '$1-$2', $relative) ?? ''),
                'snake' => strtolower(preg_replace('/([^A-Z\/])([A-Z])/', '$1_$2', $relative) ?? ''),
                default => $relative
            };

            $file = $base . $relative . '.php';

            // TODO: If I use a try-catch block around the require, can I catch
            // parser errors in the included file?
            if (file_exists($file))
                require_once $file;
        });
    };
}

loader('Psi', __DIR__ . '/src', 'kebab');
