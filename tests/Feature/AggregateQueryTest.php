<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AggregateQueryTest extends TestCase
{
    use RefreshDatabase;

    private function createPaymentMethod()
    {
        return PaymentMethod::create([
            'name' => 'Cash',
        ]);
    }

    public function test_dashboard_can_count_transactions()
    {
        $payment = $this->createPaymentMethod();

        Transaction::create([
            'invoice_no' => 'INV001',
            'customer_name' => 'A',
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        Transaction::create([
            'invoice_no' => 'INV002',
            'customer_name' => 'B',
            'total_amount' => 20000,
            'paid_amount' => 20000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $count = Transaction::count();

        $this->assertEquals(
            2,
            $count
        );
    }

    public function test_dashboard_can_calculate_total_income()
    {
        $payment = $this->createPaymentMethod();

        Transaction::create([
            'invoice_no' => 'INV003',
            'customer_name' => 'A',
            'total_amount' => 15000,
            'paid_amount' => 15000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        Transaction::create([
            'invoice_no' => 'INV004',
            'customer_name' => 'B',
            'total_amount' => 35000,
            'paid_amount' => 35000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $income = Transaction::sum('total_amount');

        $this->assertEquals(
            50000,
            $income
        );
    }

    public function test_dashboard_can_get_latest_transaction()
    {
        $payment = $this->createPaymentMethod();

        Transaction::create([
            'invoice_no' => 'INV005',
            'customer_name' => 'Old',
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now()->subDay(),
        ]);

        Transaction::create([
            'invoice_no' => 'INV006',
            'customer_name' => 'Latest',
            'total_amount' => 25000,
            'paid_amount' => 25000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $latest = Transaction::latest('transaction_date')->first();

        $this->assertEquals(
            'INV006',
            $latest->invoice_no
        );
    }

    public function test_transaction_total_can_be_calculated()
    {
        $payment = $this->createPaymentMethod();

        Transaction::create([
            'invoice_no' => 'INV007',
            'customer_name' => 'A',
            'total_amount' => 5000,
            'paid_amount' => 5000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        Transaction::create([
            'invoice_no' => 'INV008',
            'customer_name' => 'B',
            'total_amount' => 7000,
            'paid_amount' => 7000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $total = Transaction::sum('total_amount');

        $this->assertEquals(
            12000,
            $total
        );
    }

    public function test_report_can_filter_transactions()
    {
        $payment = $this->createPaymentMethod();

        Transaction::create([
            'invoice_no' => 'INV009',
            'customer_name' => 'Andi',
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        Transaction::create([
            'invoice_no' => 'INV010',
            'customer_name' => 'Budi',
            'total_amount' => 20000,
            'paid_amount' => 20000,
            'change_amount' => 0,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $filtered = Transaction::where(
            'customer_name',
            'Andi'
        )->get();

        $this->assertCount(
            1,
            $filtered
        );
    }
}
