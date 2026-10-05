<?php

it('allows access to admin routes when the panel is enabled', function () {
    config(['app.admin_panel_enabled' => true]);

    $this->get('/admin/masuk')->assertOk();
    $this->get('/admin')->assertRedirect(route('admin.login'));
});

it('returns 404 for all admin routes when the panel is disabled', function () {
    config(['app.admin_panel_enabled' => false]);

    $this->get('/admin/masuk')->assertNotFound();
    $this->get('/admin')->assertNotFound();
    $this->get('/admin/kendaraan')->assertNotFound();
    $this->post('/admin/masuk', ['nip' => '199001012015011001', 'password' => 'Admin@12345'])->assertNotFound();
});
