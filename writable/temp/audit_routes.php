<?php
/**
 * Auditoría de rutas: verifica que cada ruta apunte a un controlador/método existente
 * y que cada view() en controladores apunte a un archivo existente.
 */

$projectRoot = __DIR__ . '/../..';

// ─── 1. Extraer métodos de controladores ───
$controllerMethods = [];
$controllerDirs = [
    $projectRoot . '/app/Controllers',
    $projectRoot . '/app/Controllers/GlobalAdmin',
];

foreach ($controllerDirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $relativePath = str_replace($projectRoot . '/', '', $file);
        
        // Extraer namespace
        preg_match('/namespace\s+([^;]+);/', $content, $nsMatches);
        $namespace = $nsMatches[1] ?? '';
        
        // Extraer nombre de clase
        preg_match('/class\s+(\w+)\s+extends\s+BaseController/', $content, $classMatches);
        $className = $classMatches[1] ?? basename($file, '.php');
        
        // Extraer métodos públicos
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $methodMatches);
        foreach ($methodMatches[1] as $method) {
            $fqcn = $namespace . '\\' . $className . '::' . $method;
            $controllerMethods[$fqcn] = [
                'file' => $relativePath,
                'class' => $className,
                'method' => $method,
            ];
        }
    }
}

echo "=== MÉTODOS DE CONTROLADORES (" . count($controllerMethods) . ") ===\n";
foreach ($controllerMethods as $fqcn => $info) {
    echo "  {$info['class']}::{$info['method']}\n";
}
echo "\n";

// ─── 2. Extraer rutas y verificar contra controladores ───
$routesContent = file_get_contents($projectRoot . '/app/Config/Routes.php');

// Encontrar todas las referencias a Controller::method en rutas
preg_match_all("!(['\"])([A-Za-z0-9_\\\\\\\\]+Controller::[A-Za-z0-9_]+)(?:/\$?\d*)?\\1!", $routesContent, $routeMatches);

echo "=== RUTAS vs CONTROLADORES ===\n";
$errors = [];
$ok = 0;
$checked = [];

foreach ($routeMatches[2] as $routeTarget) {
    $cleanTarget = str_replace('\\\\', '\\', $routeTarget);
    
    $found = false;
    
    // Forma 1: App\Controllers\ClassName::method
    // Forma 2: GlobalAdmin\ClassName::method (already in namespace form)
    // Forma 3: Just ClassName::method
    
    $parts = explode('::', $cleanTarget);
    $methodOnly = $parts[1] ?? '';
    
    // Check all possible forms
    foreach ($controllerMethods as $fqcn => $info) {
        if ($info['method'] === $methodOnly) {
            // Check if the class name appears in the route target
            if (strpos($cleanTarget, $info['class']) !== false) {
                $found = true;
                break;
            }
            // Also check if the full namespace matches
            $routeClass = str_replace('App\\\\Controllers\\\\', '', $cleanTarget);
            $routeClass = str_replace('App\Controllers\\', '', $routeClass);
            $infoFull = 'App\\Controllers\\' . $info['class'];
            $routeFull = str_replace('\\\\', '\\', $routeTarget);
            if ($routeFull === $infoFull . '::' . $methodOnly) {
                $found = true;
                break;
            }
        }
    }
    
    $checkKey = $cleanTarget;
    if (!isset($checked[$checkKey])) {
        $checked[$checkKey] = true;
        if ($found) {
            $ok++;
        } else {
            $errors[] = "  ❌ RUTA ROTA: {$routeTarget}";
        }
    }
}

if (empty($errors)) {
    echo "  ✅ {$ok} rutas verificadas — todas OK\n";
} else {
    echo implode("\n", $errors) . "\n";
}
echo "\n";

// ─── 3. Extraer view() calls y verificar contra archivos ───
echo "=== VISTAS REFERENCIADAS vs ARCHIVOS EN DISCO ===\n";

// Escanear archivos de vista
$viewFiles = [];
$viewDir = $projectRoot . '/app/Views';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewDir, RecursiveDirectoryIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    if ($file->getExtension() === 'php') {
        $relative = str_replace($viewDir . '/', '', $file->getPathname());
        $relative = str_replace('\\', '/', $relative);
        $relative = preg_replace('/\.php$/', '', $relative);
        $viewFiles[$relative] = true;
    }
}

// Extraer view('...') de todos los controladores
$allControllers = array_merge(
    glob($projectRoot . '/app/Controllers/*.php') ?: [],
    glob($projectRoot . '/app/Controllers/GlobalAdmin/*.php') ?: []
);

$viewErrors = [];
$viewOk = 0;
$viewChecked = [];

foreach ($allControllers as $ctrlFile) {
    $content = file_get_contents($ctrlFile);
    preg_match_all("!view\s*\(\s*(['\"])([^'\"]+)\\1!s", $content, $viewMatches);
    
    foreach ($viewMatches[2] as $viewName) {
        $viewName = str_replace('\\', '/', $viewName);
        if (!isset($viewChecked[$viewName])) {
            $viewChecked[$viewName] = true;
            if (isset($viewFiles[$viewName])) {
                $viewOk++;
            } else {
                $viewErrors[] = "  ❌ VISTA FALTANTE: {$viewName}";
            }
        }
    }
}

if (empty($viewErrors)) {
    echo "  ✅ {$viewOk} vistas verificadas — todas existen\n";
} else {
    echo implode("\n", $viewErrors) . "\n";
}
echo "\n";

// ─── 4. Vistas huérfanas ───
echo "=== VISTAS HUÉRFANAS ===\n";
$orphanViews = [];
foreach ($viewFiles as $viewName => $true) {
    if (strpos($viewName, 'layouts/') === 0 || 
        strpos($viewName, 'partials/') === 0 ||
        strpos($viewName, 'errors/') === 0) {
        continue;
    }
    if (!isset($viewChecked[$viewName])) {
        $orphanViews[] = $viewName;
    }
}

if (empty($orphanViews)) {
    echo "  ✅ No hay vistas huérfanas\n";
} else {
    foreach ($orphanViews as $v) {
        echo "  👻 NO REFERENCIADA: {$v}\n";
    }
}
echo "\n";

// ─── 5. Verificar include/require en vistas ───
echo "=== PARTIALS INCLUIDOS vs ARCHIVOS ===\n";
$partialErrors = [];

foreach ($allControllers as $ctrlFile) {
    $content = file_get_contents($ctrlFile);
    // Buscar include/require de vistas
    preg_match_all("!(include|require)(_once)?\s*\(\s*(['\"])([^'\"]+)\\3!s", $content, $includeMatches);
    foreach ($includeMatches[4] as $includePath) {
        $fullPath = $projectRoot . '/' . $includePath;
        if (!file_exists($fullPath)) {
            $partialErrors[] = "  ❌ INCLUDE FALTANTE en {$ctrlFile}: {$includePath}";
        }
    }
}

if (empty($partialErrors)) {
    echo "  ✅ No hay includes rotos en controladores\n";
} else {
    echo implode("\n", $partialErrors) . "\n";
}

echo "\n✅ AUDITORÍA COMPLETADA\n";
