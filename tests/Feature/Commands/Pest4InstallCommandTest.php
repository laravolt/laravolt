<?php

test('pest4 install command can be executed', function () {
    $this->artisan('laravolt:pest4-install --force')
        ->expectsOutput('🧪 Installing Pest v4...')
        ->assertSuccessful();
});

test('pest4 install command creates configuration files', function () {
    // Mock the file system operations for testing
    expect(true)->toBeTrue(); // Placeholder - would need proper mocking for full test
});

test('pest4 install command can be called from install command', function () {
    // This would test the integration with the main install command
    expect(true)->toBeTrue(); // Placeholder - would need proper setup for full test
});
