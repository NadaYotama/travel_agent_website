<?php

namespace Tests\Unit;

use App\Services\MidtransPaymentService;
use Tests\TestCase;

class MidtransPaymentServiceTest extends TestCase
{
    public function test_midtrans_notification_signature_is_verified(): void
    {
        $serverKey = 'test-server-key';
        config(['services.midtrans.server_key' => $serverKey]);
        $notification = [
            'order_id' => 'BOOK-1-PAY-1',
            'status_code' => '200',
            'gross_amount' => '125000.00',
            'signature_key' => hash('sha512', 'BOOK-1-PAY-1' . '200' . '125000.00' . $serverKey),
        ];

        $service = new MidtransPaymentService();

        $this->assertTrue($service->isValidSignature($notification));
        $notification['gross_amount'] = '125001.00';
        $this->assertFalse($service->isValidSignature($notification));
    }

    public function test_amount_comparison_uses_rupiah_precision(): void
    {
        $service = new MidtransPaymentService();

        $this->assertTrue($service->amountsMatch('125000.00', '125000'));
        $this->assertFalse($service->amountsMatch('125000.00', '125000.01'));
        $this->assertFalse($service->amountsMatch('125000.00', 'invalid'));
    }
}
