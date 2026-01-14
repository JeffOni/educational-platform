<?php
// Test database connection
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DE CONEXIÓN A BASE DE DATOS ===\n\n";

echo "DB_DATABASE desde .env: " . env('DB_DATABASE') . "\n";
echo "DB_DATABASE desde config: " . config('database.connections.mysql.database') . "\n\n";

try {
    $pdo = DB::connection()->getPdo();
    echo "✓ Conexión exitosa!\n";
    echo "Base de datos actual: " . DB::connection()->getDatabaseName() . "\n";
    
    // Intentar contar cursos
    $count = DB::table('courses')->count();
    echo "Total de cursos en la BD: " . $count . "\n";
    
} catch (Exception $e) {
    echo "✗ Error de conexión:\n";
    echo $e->getMessage() . "\n";
}
