<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\MidtransService;

class MidtransServiceTest extends TestCase
{
    public function test_midtrans_service_configuration()
    {
        $service = new MidtransService();
        
        $this->assertNotNull(config('services.midtrans.server_key'));
        $this->assertNotNull(config('services.midtrans.client_key'));
        $this->assertNotNull(config('services.midtrans.is_production'));
    }
}
