<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    /**
     * QR Generation Test
     *
     * @return void
     */
    public function test_the_qr_generate_successfully()
    {
        $response = $this->get('test');

        $response->assertStatus(200);
    }
}
