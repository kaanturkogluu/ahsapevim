<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blade = app('blade.compiler');
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));

$hasError = false;

foreach ($rii as $file) {
    if ($file->isDir()) continue;
    if (!str_ends_with($file->getPathname(), '.blade.php')) continue;

    $content = file_get_contents($file->getPathname());
    
    try {
        $compiled = $blade->compileString($content);
        
        // Lint the compiled PHP code
        $tmpFile = tempnam(sys_get_temp_dir(), 'blade_lint_');
        file_put_contents($tmpFile, $compiled);
        
        $output = [];
        $returnVar = 0;
        exec("php -l " . escapeshellarg($tmpFile) . " 2>&1", $output, $returnVar);
        unlink($tmpFile);
        
        if ($returnVar !== 0) {
            echo "ERROR in {$file->getPathname()}:\n" . implode("\n", $output) . "\n\n";
            $hasError = true;
        }
    } catch (\Throwable $e) {
        echo "COMPILATION ERROR in {$file->getPathname()}: " . $e->getMessage() . "\n";
        $hasError = true;
    }
}

if (!$hasError) {
    echo "All Blade templates passed php -l lint successfully!\n";
}
