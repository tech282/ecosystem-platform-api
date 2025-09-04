<?php

echo "🚀 Laravel API Status Check\n";
echo "==========================\n\n";

// Check Laravel installation
echo "✅ Laravel Framework: " . app()->version() . "\n";

// Check environment
echo "✅ Environment: " . app()->environment() . "\n";

// Check configuration
echo "✅ App Name: " . config('app.name') . "\n";
echo "✅ App URL: " . config('app.url') . "\n";

// Check database configuration
echo "✅ Database Connection: " . config('database.default') . "\n";
echo "✅ Database Host: " . config('database.connections.' . config('database.default') . '.host') . "\n";
echo "✅ Database Name: " . config('database.connections.' . config('database.default') . '.database') . "\n";

// Check routes
$router = app('router');
$routes = $router->getRoutes();
$apiRoutes = collect($routes)->filter(function ($route) {
    return str_starts_with($route->uri(), 'api/');
});

echo "✅ API Routes Loaded: " . $apiRoutes->count() . " routes\n";

// Check key services
echo "✅ App Key Set: " . (config('app.key') ? 'Yes' : 'No') . "\n";

// List some API endpoints
echo "\n📋 Available API Endpoints:\n";
foreach ($apiRoutes->take(10) as $route) {
    $methods = implode('|', $route->methods());
    echo "   {$methods} /{$route->uri()}\n";
}

echo "\n🎉 Laravel API is configured and ready!\n";
echo "Next step: Configure database connection and start server.\n";
