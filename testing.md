PS D:\laragon\www\pos_laravel> php artisan make:test RoleUserTest

   INFO  Test [D:\laragon\www\pos_laravel\tests\Feature\RoleUserTest.php] created successfully.  

PS D:\laragon\www\pos_laravel> php artisan test --filter=RoleUserTest

   PASS  Tests\Feature\RoleUserTest
  ✓ user can create role                                                                                             0.44s  
  ✓ role permissions are saved                                                                                       0.02s  
  ✓ user can update role                                                                                             0.02s  
  ✓ user can delete role                                                                                             0.02s  
  ✓ user can create user                                                                                             0.03s  
  ✓ user can update user                                                                                             0.02s  
  ✓ user can verify user                                                                                             0.02s  
  ✓ user can delete user                                                                                             0.02s  

  Tests:    8 passed (23 assertions)
  Duration: 0.83s

PS D:\laragon\www\pos_laravel> php artisan test --coverage

   ERROR  Code coverage driver not available. Did you install Xdebug or PCOV?

PS D:\laragon\www\pos_laravel> php artisan test --coverage

   PASS  Tests\Unit\UserPermissionTest
  ✓ permission mapping for index route                                                                               0.38s  
  ✓ permission mapping for store route                                                                               0.05s  
  ✓ user has exact permission                                                                                        0.05s  
  ✓ user has wildcard permission                                                                                     0.05s  
  ✓ user does not have invalid permission                                                                            0.04s  
  ✓ user without role has no permission                                                                              0.07s  
  ✓ get first accessible route                                                                                       0.04s  

   PASS  Tests\Feature\AuthTest
  ✓ user can login with valid credentials                                                                            0.63s  
  ✓ user cannot login with invalid credentials                                                                       0.28s  
  ✓ user can logout                                                                                                  0.06s  
  ✓ user can update profile                                                                                          0.07s  

   FAIL  Tests\Feature\CashFlowTest
  ⨯ user can create cash in flow                                                                                     0.20s  
  ⨯ user can create cash out flow                                                                                    0.07s  
  ⨯ cash flow fails when source type mismatch                                                                        0.07s  
  ✓ manual cashflow can be deleted                                                                                   0.07s  
  ✓ auto cashflow cannot be deleted                                                                                  0.07s  

   PASS  Tests\Feature\PaymentMethodTest
  ✓ user can create payment method                                                                                   0.17s  
  ✓ payment method validation fails                                                                                  0.09s  
  ✓ user can update payment method                                                                                   0.07s  
  ✓ user can soft delete payment method                                                                              0.06s  
  ✓ user can restore payment method                                                                                  0.06s  
  ✓ is cash payment method logic                                                                                     0.05s  

   PASS  Tests\Feature\PermissionMiddlewareTest
  ✓ guest is redirected to login                                                                                     0.12s  
  ✓ user without permission gets 403                                                                                 0.07s  
  ✓ user with permission can access dashboard                                                                        0.10s  
  ✓ json request without permission returns 403                                                                      0.06s  

   PASS  Tests\Feature\PosTest
  ✓ pos transaction success                                                                                          0.16s  
  ✓ pos fails when stock is insufficient                                                                             0.08s  
  ✓ pos fails when payment is insufficient                                                                           0.07s  
  ✓ pos fails when items are empty                                                                                   0.06s  

   PASS  Tests\Feature\ProdukTest
  ✓ user can create product                                                                                          0.18s  
  ✓ product validation fails                                                                                         0.09s  
  ✓ user can update product                                                                                          0.07s  
  ✓ user can soft delete product                                                                                     0.07s  

   PASS  Tests\Feature\RoleUserTest
  ✓ user can create role                                                                                             0.09s  
  ✓ role permissions are saved                                                                                       0.06s  
  ✓ user can update role                                                                                             0.08s  
  ✓ user can delete role                                                                                             0.07s  
  ✓ user can create user                                                                                             0.09s  
  ✓ user can update user                                                                                             0.07s  
  ✓ user can verify user                                                                                             0.08s  
  ✓ user can delete user                                                                                             0.07s  

   PASS  Tests\Feature\TransaksiTest
  ✓ user can view transaction index                                                                                  0.10s  
  ✓ user can view transaction detail                                                                                 0.11s  
  ✓ destroy transaction restores stock and deletes cashflow                                                          0.09s  
  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > user can create cash in flow                                                        
  Expected response status code [302] but received 201.
Failed asserting that 201 is identical to 302.

  at tests\Feature\CashFlowTest.php:70
     66▕             'date' => now()->format('Y-m-d'),
     67▕             'notes' => 'Pemasukan harian',
     68▕         ]);
     69▕ 
  ➜  70▕         $response->assertStatus(302);
     71▕ 
     72▕         $this->assertDatabaseHas('cashbox_flows', [
     73▕             'type' => 'in',
     74▕             'amount' => 100000,

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > user can create cash out flow                                                       
  Expected response status code [302] but received 201.
Failed asserting that 201 is identical to 302.

  at tests\Feature\CashFlowTest.php:94
     90▕             'date' => now()->format('Y-m-d'),
     91▕             'notes' => 'Biaya listrik',
     92▕         ]);
     93▕ 
  ➜  94▕         $response->assertStatus(302);
     95▕ 
     96▕         $this->assertDatabaseHas('cashbox_flows', [
     97▕             'type' => 'out',
     98▕             'amount' => 50000,

  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > cash flow fails when source type mismatch                                           
  Session is missing expected key [errors].
Failed asserting that false is true.

  at tests\Feature\CashFlowTest.php:118
    114▕             'date' => now()->format('Y-m-d'),
    115▕             'notes' => 'Invalid source',
    116▕         ]);
    117▕ 
  ➜ 118▕         $response->assertSessionHasErrors();
    119▕     }
    120▕ 
    121▕     public function test_manual_cashflow_can_be_deleted()
    122▕     {


  Tests:    3 failed, 42 passed (106 assertions)
  Duration: 5.19s

PS D:\laragon\www\pos_laravel> php artisan make:test RelationshipTest

   INFO  Test [D:\laragon\www\pos_laravel\tests\Feature\RelationshipTest.php] created successfully.  

PS D:\laragon\www\pos_laravel> php artisan test --filter=RelationshipTest

   PASS  Tests\Feature\RelationshipTest
  ✓ category has products relation                                                                                   0.60s  
  ✓ transaction has transaction items relation                                                                       0.07s  
  ✓ transaction belongs to payment method                                                                            0.05s  
  ✓ transaction item belongs to product                                                                              0.06s  
  ✓ payment method has transactions relation                                                                         0.06s  

  Tests:    5 passed (5 assertions)
  Duration: 1.15s

PS D:\laragon\www\pos_laravel> php artisan make:test ModelStateTest

   INFO  Test [D:\laragon\www\pos_laravel\tests\Feature\ModelStateTest.php] created successfully.  

PS D:\laragon\www\pos_laravel> php artisan test --filter=ModelStateTest

   PASS  Tests\Feature\ModelStateTest
  ✓ product has default stock zero                                                                                   0.46s  
  ✓ product price is casted to decimal                                                                               0.05s  
  ✓ payment method active status can be true                                                                         0.03s  
  ✓ payment method active status can be false                                                                        0.04s  
  ✓ product can be soft deleted                                                                                      0.03s  
  ✓ payment method can be soft deleted                                                                               0.03s  

  Tests:    6 passed (6 assertions)
  Duration: 0.88s

PS D:\laragon\www\pos_laravel> php artisan make:test ArithmeticLogicTest

   INFO  Test [D:\laragon\www\pos_laravel\tests\Feature\ArithmeticLogicTest.php] created successfully.  

PS D:\laragon\www\pos_laravel> php artisan test --filter=ArithmeticLogicTest

   PASS  Tests\Feature\ArithmeticLogicTest
  ✓ calculate total price correctly                                                                                  0.46s  
  ✓ change calculation correct                                                                                       0.03s  
  ✓ change becomes zero if cash less than total                                                                      0.03s  
  ✓ cannot add quantity more than stock                                                                              0.04s  
  ✓ checkout fails if cart empty                                                                                     0.03s  
  ✓ subtotal calculation correct                                                                                     0.03s  
  ✓ total transaction from multiple items                                                                            0.04s  

  Tests:    7 passed (7 assertions)
  Duration: 0.92s

PS D:\laragon\www\pos_laravel> php artisan make:test AggregateQueryTest

   INFO  Test [D:\laragon\www\pos_laravel\tests\Feature\AggregateQueryTest.php] created successfully.  

PS D:\laragon\www\pos_laravel> <?php
>> 
>> namespace Tests\Feature;
>> 
>> use Tests\TestCase;
>> use App\Models\Transaction;
>> use App\Models\PaymentMethod;
>> use Illuminate\Foundation\Testing\RefreshDatabase;
>> 
>> class AggregateQueryTest extends TestCase
>> {
>>     use RefreshDatabase;
>> 
>>     private function createPaymentMethod()
>>     {
>>         return PaymentMethod::create([
>>             'name' => 'Cash',
>>         ]);
>>     }
>> 
>>     public function test_dashboard_can_count_transactions()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV001',
>>             'customer_name' => 'A',
>>             'total_amount' => 10000,
>>             'paid_amount' => 10000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV002',
>>             'customer_name' => 'B',
>>             'total_amount' => 20000,
>>             'paid_amount' => 20000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $count = Transaction::count();
>> 
>>         $this->assertEquals(
>>             2,
>>             $count
>>         );
>>     }
>> 
>>     public function test_dashboard_can_calculate_total_income()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV003',
>>             'customer_name' => 'A',
>>             'total_amount' => 15000,
>>             'paid_amount' => 15000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV004',
>>             'customer_name' => 'B',
>>             'total_amount' => 35000,
>>             'paid_amount' => 35000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $income = Transaction::sum('total_amount');
>> 
>>         $this->assertEquals(
>>             50000,
>>             $income
>>         );
>>     }
>> 
>>     public function test_dashboard_can_get_latest_transaction()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV005',
>>             'customer_name' => 'Old',
>>             'total_amount' => 10000,
>>             'paid_amount' => 10000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now()->subDay(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV006',
>>             'customer_name' => 'Latest',
>>             'total_amount' => 25000,
>>             'paid_amount' => 25000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $latest = Transaction::latest('transaction_date')->first();
>> 
>>         $this->assertEquals(
>>             'INV006',
>>             $latest->invoice_no
>>         );
>>     }
>> 
>>     public function test_transaction_total_can_be_calculated()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV007',
>>             'customer_name' => 'A',
>>             'total_amount' => 5000,
>>             'paid_amount' => 5000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV008',
>>             'customer_name' => 'B',
>>             'total_amount' => 7000,
>>             'paid_amount' => 7000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $total = Transaction::sum('total_amount');
>> 
>>         $this->assertEquals(
>>             12000,
>>             $total
>>         );
>>     }
>> 
>>     public function test_report_can_filter_transactions()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV009',
>>             'customer_name' => 'Andi',
>>             'total_amount' => 10000,
>>             'paid_amount' => 10000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV010',
>>             'customer_name' => 'Budi',
>>             'total_amount' => 20000,
>>             'paid_amount' => 20000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $filtered = Transaction::where(
>>             'customer_name',
>>             'Andi'
>>         )->get();
>> 
>>         $this->assertCount(
>>             1,
>>             $filtered
>>         );
>>     }
>> }
At line:10 char:25
+ class AggregateQueryTest extends TestCase
+                         ~
Missing 'class' body in 'class' declaration.
At line:14 char:42
+     private function createPaymentMethod()
+                                          ~
An expression was expected after '('.
At line:16 char:39
+         return PaymentMethod::create([
+                                       ~
Missing type name after '['.
At line:16 char:39
+         return PaymentMethod::create([
+                                       ~
Missing closing ')' in expression.
At line:15 char:5
+     {
+     ~
Missing closing '}' in statement block or type definition.
At line:11 char:1
+ {
+ ~
Missing closing '}' in statement block or type definition.
At line:18 char:10
+         ]);
+          ~
Unexpected token ')' in expression or statement.
At line:19 char:5
+     }
+     ~
Unexpected token '}' in expression or statement.
At line:21 char:59
+     public function test_dashboard_can_count_transactions()
+                                                           ~
An expression was expected after '('.
At line:23 char:26
+         $payment = $this->createPaymentMethod();
+                          ~
You must provide a value expression following the '-' operator.
Not all parse errors were reported.  Correct the reported errors and try again.
    + CategoryInfo          : ParserError: (:) [], ParentContainsErrorRecordException
    + FullyQualifiedErrorId : MissingTypeBody
 
PS D:\laragon\www\pos_laravel> php artisan test --filter=AggregateQueryTest

   PASS  Tests\Feature\AggregateQueryTest
  ✓ dashboard can count transactions                                                                                 0.46s  
  ✓ dashboard can calculate total income                                                                             0.04s  
  ✓ dashboard can get latest transaction                                                                             0.04s  
  ✓ transaction total can be calculated                                                                              0.04s  
  ✓ report can filter transactions                                                                                   0.04s  

  Tests:    5 passed (5 assertions)
  Duration: 0.87s

PS D:\laragon\www\pos_laravel> <?php                                       
>> 
>> namespace Tests\Feature;
>> 
>> use Tests\TestCase;
>> use App\Models\Transaction;
>> use App\Models\PaymentMethod;
>> use Illuminate\Foundation\Testing\RefreshDatabase;
>> 
>> class AggregateQueryTest extends TestCase
>> {
>>     use RefreshDatabase;
>> 
>>     private function createPaymentMethod()
>>     {
>>         return PaymentMethod::create([
>>             'name' => 'Cash',
>>         ]);
>>     }
>> 
>>     public function test_dashboard_can_count_transactions()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV001',
>>             'customer_name' => 'A',
>>             'total_amount' => 10000,
>>             'paid_amount' => 10000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV002',
>>             'customer_name' => 'B',
>>             'total_amount' => 20000,
>>             'paid_amount' => 20000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $count = Transaction::count();
>> 
>>         $this->assertEquals(
>>             2,
>>             $count
>>         );
>>     }
>> 
>>     public function test_dashboard_can_calculate_total_income()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV003',
>>             'customer_name' => 'A',
>>             'total_amount' => 15000,
>>             'paid_amount' => 15000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV004',
>>             'customer_name' => 'B',
>>             'total_amount' => 35000,
>>             'paid_amount' => 35000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $income = Transaction::sum('total_amount');
>> 
>>         $this->assertEquals(
>>             50000,
>>             $income
>>         );
>>     }
>> 
>>     public function test_dashboard_can_get_latest_transaction()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV005',
>>             'customer_name' => 'Old',
>>             'total_amount' => 10000,
>>             'paid_amount' => 10000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now()->subDay(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV006',
>>             'customer_name' => 'Latest',
>>             'total_amount' => 25000,
>>             'paid_amount' => 25000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $latest = Transaction::latest('transaction_date')->first();
>> 
>>         $this->assertEquals(
>>             'INV006',
>>             $latest->invoice_no
>>         );
>>     }
>> 
>>     public function test_transaction_total_can_be_calculated()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV007',
>>             'customer_name' => 'A',
>>             'total_amount' => 5000,
>>             'paid_amount' => 5000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV008',
>>             'customer_name' => 'B',
>>             'total_amount' => 7000,
>>             'paid_amount' => 7000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $total = Transaction::sum('total_amount');
>> 
>>         $this->assertEquals(
>>             12000,
>>             $total
>>         );
>>     }
>> 
>>     public function test_report_can_filter_transactions()
>>     {
>>         $payment = $this->createPaymentMethod();
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV009',
>>             'customer_name' => 'Andi',
>>             'total_amount' => 10000,
>>             'paid_amount' => 10000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         Transaction::create([
>>             'invoice_no' => 'INV010',
>>             'customer_name' => 'Budi',
>>             'total_amount' => 20000,
>>             'paid_amount' => 20000,
>>             'change_amount' => 0,
>>             'payment_method_id' => $payment->id,
>>             'status' => 'completed',
>>             'transaction_date' => now(),
>>         ]);
>> 
>>         $filtered = Transaction::where(
>>             'customer_name',
>>             'Andi'
>>         )->get();
>> 
>>         $this->assertCount(
>>             1,
>>             $filtered
>>         );
>>     }
>> }
PS D:\laragon\www\pos_laravel> php artisan test --coverage                  

   PASS  Tests\Unit\UserPermissionTest
  ✓ permission mapping for index route                                     0.52s  
  ✓ permission mapping for store route                                     0.07s  
  ✓ user has exact permission                                              0.07s  
  ✓ user has wildcard permission                                           0.11s  
  ✓ user does not have invalid permission                                  0.09s  
  ✓ user without role has no permission                                    0.14s  
  ✓ get first accessible route                                             0.07s  

   PASS  Tests\Feature\AggregateQueryTest
  ✓ dashboard can count transactions                                       0.62s  
  ✓ dashboard can calculate total income                                   0.09s  
  ✓ dashboard can get latest transaction                                   0.07s  
  ✓ transaction total can be calculated                                    0.10s  
  ✓ report can filter transactions                                         0.08s  

   PASS  Tests\Feature\ArithmeticLogicTest
  ✓ calculate total price correctly                                        0.11s  
  ✓ change calculation correct                                             0.08s  
  ✓ change becomes zero if cash less than total                            0.06s  
  ✓ cannot add quantity more than stock                                    0.09s  
  ✓ checkout fails if cart empty                                           0.07s  
  ✓ subtotal calculation correct                                           0.07s  
  ✓ total transaction from multiple items                                  0.07s  

   PASS  Tests\Feature\AuthTest
  ✓ user can login with valid credentials                                  0.45s  
  ✓ user cannot login with invalid credentials                             0.30s  
  ✓ user can logout                                                        0.12s  
  ✓ user can update profile                                                0.13s  

   FAIL  Tests\Feature\CashFlowTest
  ⨯ user can create cash in flow                                           0.28s  
  ⨯ user can create cash out flow                                          0.12s  
  ⨯ cash flow fails when source type mismatch                              0.09s  
  ✓ manual cashflow can be deleted                                         0.10s  
  ✓ auto cashflow cannot be deleted                                        0.13s  

   PASS  Tests\Feature\ModelStateTest
  ✓ product has default stock zero                                         0.08s  
  ✓ product price is casted to decimal                                     0.09s  
  ✓ payment method active status can be true                               0.06s  
  ✓ payment method active status can be false                              0.10s  
  ✓ product can be soft deleted                                            0.08s  
  ✓ payment method can be soft deleted                                     0.07s  

   PASS  Tests\Feature\PaymentMethodTest
  ✓ user can create payment method                                         0.32s  
  ✓ payment method validation fails                                        0.09s  
  ✓ user can update payment method                                         0.11s  
  ✓ user can soft delete payment method                                    0.08s  
  ✓ user can restore payment method                                        0.12s  
  ✓ is cash payment method logic                                           0.13s  

   PASS  Tests\Feature\PermissionMiddlewareTest
  ✓ guest is redirected to login                                           0.15s  
  ✓ user without permission gets 403                                       0.13s  
  ✓ user with permission can access dashboard                              0.13s  
  ✓ json request without permission returns 403                            0.15s  

   PASS  Tests\Feature\PosTest
  ✓ pos transaction success                                                0.18s  
  ✓ pos fails when stock is insufficient                                   0.14s  
  ✓ pos fails when payment is insufficient                                 0.13s  
  ✓ pos fails when items are empty                                         0.08s  

   PASS  Tests\Feature\ProdukTest
  ✓ user can create product                                                0.33s  
  ✓ product validation fails                                               0.09s  
  ✓ user can update product                                                0.11s  
  ✓ user can soft delete product                                           0.10s  

   PASS  Tests\Feature\RelationshipTest
  ✓ category has products relation                                         0.13s  
  ✓ transaction has transaction items relation                             0.11s  
  ✓ transaction belongs to payment method                                  0.10s  
  ✓ transaction item belongs to product                                    0.11s  
  ✓ payment method has transactions relation                               0.10s  

   PASS  Tests\Feature\RoleUserTest
  ✓ user can create role                                                   0.21s  
  ✓ role permissions are saved                                             0.10s  
  ✓ user can update role                                                   0.12s  
  ✓ user can delete role                                                   0.12s  
  ✓ user can create user                                                   0.16s  
  ✓ user can update user                                                   0.12s  
  ✓ user can verify user                                                   0.13s  
  ✓ user can delete user                                                   0.12s  

   PASS  Tests\Feature\TransaksiTest
  ✓ user can view transaction index                                        0.19s  
  ✓ user can view transaction detail                                       0.18s  
  ✓ destroy transaction restores stock and deletes cashflow                0.18s  
  ──────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > user can create cash in flow              
  Expected response status code [302] but received 201.
Failed asserting that 201 is identical to 302.

  at tests\Feature\CashFlowTest.php:70
     66▕             'date' => now()->format('Y-m-d'),
     67▕             'notes' => 'Pemasukan harian',
     68▕         ]);
     69▕ 
  ➜  70▕         $response->assertStatus(302);
     71▕ 
     72▕         $this->assertDatabaseHas('cashbox_flows', [
     73▕             'type' => 'in',
     74▕             'amount' => 100000,

  ──────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > user can create cash out flow             
  Expected response status code [302] but received 201.
Failed asserting that 201 is identical to 302.

  at tests\Feature\CashFlowTest.php:94
     90▕             'date' => now()->format('Y-m-d'),
     91▕             'notes' => 'Biaya listrik',
     92▕         ]);
     93▕ 
  ➜  94▕         $response->assertStatus(302);
     95▕ 
     96▕         $this->assertDatabaseHas('cashbox_flows', [
     97▕             'type' => 'out',
     98▕             'amount' => 50000,

  ──────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > cash flow fails when source type misma…   
  Session is missing expected key [errors].
Failed asserting that false is true.

  at tests\Feature\CashFlowTest.php:118
    114▕             'date' => now()->format('Y-m-d'),
    115▕             'notes' => 'Invalid source',
    116▕         ]);
    117▕ 
  ➜ 118▕         $response->assertSessionHasErrors();
    119▕     }
    120▕ 
    121▕     public function test_manual_cashflow_can_be_deleted()
    122▕     {


  Tests:    3 failed, 65 passed (129 assertions)
  Duration: 10.42s

PS D:\laragon\www\pos_laravel> php artisan test

   PASS  Tests\Unit\UserPermissionTest
  ✓ permission mapping for index route                                     0.29s  
  ✓ permission mapping for store route                                     0.05s  
  ✓ user has exact permission                                              0.04s  
  ✓ user has wildcard permission                                           0.04s  
  ✓ user does not have invalid permission                                  0.05s  
  ✓ user without role has no permission                                    0.07s  
  ✓ get first accessible route                                             0.06s  

   PASS  Tests\Feature\AggregateQueryTest
  ✓ dashboard can count transactions                                       0.32s  
  ✓ dashboard can calculate total income                                   0.05s  
  ✓ dashboard can get latest transaction                                   0.05s  
  ✓ transaction total can be calculated                                    0.04s  
  ✓ report can filter transactions                                         0.05s  

   PASS  Tests\Feature\ArithmeticLogicTest
  ✓ calculate total price correctly                                        0.06s  
  ✓ change calculation correct                                             0.06s  
  ✓ change becomes zero if cash less than total                            0.04s  
  ✓ cannot add quantity more than stock                                    0.04s  
  ✓ checkout fails if cart empty                                           0.04s  
  ✓ subtotal calculation correct                                           0.05s  
  ✓ total transaction from multiple items                                  0.05s  

   PASS  Tests\Feature\AuthTest
  ✓ user can login with valid credentials                                  0.13s  
  ✓ user cannot login with invalid credentials                             0.28s  
  ✓ user can logout                                                        0.10s  
  ✓ user can update profile                                                0.09s  

   FAIL  Tests\Feature\CashFlowTest
  ⨯ user can create cash in flow                                           0.11s  
  ⨯ user can create cash out flow                                          0.09s  
  ⨯ cash flow fails when source type mismatch                              0.06s  
  ✓ manual cashflow can be deleted                                         0.08s  
  ✓ auto cashflow cannot be deleted                                        0.05s  

   PASS  Tests\Feature\ModelStateTest
  ✓ product has default stock zero                                         0.05s  
  ✓ product price is casted to decimal                                     0.04s  
  ✓ payment method active status can be true                               0.04s  
  ✓ payment method active status can be false                              0.04s  
  ✓ product can be soft deleted                                            0.06s  
  ✓ payment method can be soft deleted                                     0.04s  

   PASS  Tests\Feature\PaymentMethodTest
  ✓ user can create payment method                                         0.13s  
  ✓ payment method validation fails                                        0.06s  
  ✓ user can update payment method                                         0.08s  
  ✓ user can soft delete payment method                                    0.07s  
  ✓ user can restore payment method                                        0.06s  
  ✓ is cash payment method logic                                           0.04s  

   PASS  Tests\Feature\PermissionMiddlewareTest
  ✓ guest is redirected to login                                           0.08s  
  ✓ user without permission gets 403                                       0.07s  
  ✓ user with permission can access dashboard                              0.08s  
  ✓ json request without permission returns 403                            0.06s  

   PASS  Tests\Feature\PosTest
  ✓ pos transaction success                                                0.12s  
  ✓ pos fails when stock is insufficient                                   0.14s  
  ✓ pos fails when payment is insufficient                                 0.14s  
  ✓ pos fails when items are empty                                         0.06s  

   PASS  Tests\Feature\ProdukTest
  ✓ user can create product                                                0.07s  
  ✓ product validation fails                                               0.08s  
  ✓ user can update product                                                0.08s  
  ✓ user can soft delete product                                           0.06s  

   PASS  Tests\Feature\RelationshipTest
  ✓ category has products relation                                         0.06s  
  ✓ transaction has transaction items relation                             0.05s  
  ✓ transaction belongs to payment method                                  0.04s  
  ✓ transaction item belongs to product                                    0.05s  
  ✓ payment method has transactions relation                               0.07s  

   PASS  Tests\Feature\RoleUserTest
  ✓ user can create role                                                   0.06s  
  ✓ role permissions are saved                                             0.05s  
  ✓ user can update role                                                   0.09s  
  ✓ user can delete role                                                   0.06s  
  ✓ user can create user                                                   0.07s  
  ✓ user can update user                                                   0.06s  
  ✓ user can verify user                                                   0.09s  
  ✓ user can delete user                                                   0.07s  

   PASS  Tests\Feature\TransaksiTest
  ✓ user can view transaction index                                        0.08s  
  ✓ user can view transaction detail                                       0.07s  
  ✓ destroy transaction restores stock and deletes cashflow                0.10s  
  ──────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > user can create cash in flow              
  Expected response status code [302] but received 201.
Failed asserting that 201 is identical to 302.

  at tests\Feature\CashFlowTest.php:70
     66▕             'date' => now()->format('Y-m-d'),
     67▕             'notes' => 'Pemasukan harian',
     68▕         ]);
     69▕ 
  ➜  70▕         $response->assertStatus(302);
     71▕ 
     72▕         $this->assertDatabaseHas('cashbox_flows', [
     73▕             'type' => 'in',
     74▕             'amount' => 100000,

  ──────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > user can create cash out flow             
  Expected response status code [302] but received 201.
Failed asserting that 201 is identical to 302.

  at tests\Feature\CashFlowTest.php:94
     90▕             'date' => now()->format('Y-m-d'),
     91▕             'notes' => 'Biaya listrik',
     92▕         ]);
     93▕ 
  ➜  94▕         $response->assertStatus(302);
     95▕ 
     96▕         $this->assertDatabaseHas('cashbox_flows', [
     97▕             'type' => 'out',
     98▕             'amount' => 50000,

  ──────────────────────────────────────────────────────────────────────────────  
   FAILED  Tests\Feature\CashFlowTest > cash flow fails when source type misma…   
  Session is missing expected key [errors].
Failed asserting that false is true.

  at tests\Feature\CashFlowTest.php:118
    114▕             'date' => now()->format('Y-m-d'),
    115▕             'notes' => 'Invalid source',
    116▕         ]);
    117▕ 
  ➜ 118▕         $response->assertSessionHasErrors();
    119▕     }
    120▕ 
    121▕     public function test_manual_cashflow_can_be_deleted()
    122▕     {


  Tests:    3 failed, 65 passed (129 assertions)
  Duration: 5.71s

PS D:\laragon\www\pos_laravel> 