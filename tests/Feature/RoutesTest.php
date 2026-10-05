<?php

it('exposes no API routes: the app is a server-rendered monolith', function () {
    // The default Laravel scaffold used to register GET /api/user, which crashed (Sanctum is not installed).
    $this->getJson('/api/user')->assertNotFound();

    $apiRoutes = collect(app('router')->getRoutes()->getRoutes())->filter(fn ($route) => str_starts_with($route->uri(), 'api'));

    expect($apiRoutes)->toBeEmpty();
});
