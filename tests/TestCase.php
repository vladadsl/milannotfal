<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Vite;

abstract class TestCase extends BaseTestCase
{
    /**
     * Prepare the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $hotPath = storage_path('framework/testing/vite.hot');

        if (! is_dir(dirname($hotPath))) {
            mkdir(dirname($hotPath), 0755, true);
        }

        if (! file_exists($hotPath)) {
            file_put_contents($hotPath, 'http://localhost:5173');
        }

        Vite::useHotFile($hotPath);
    }
}
