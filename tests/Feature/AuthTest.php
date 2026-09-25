<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_customer_list(): void
    {
        $admin = User::create([
            'username' => 'testadmin',
            'password' => bcrypt('password123'),
            'role' => 'SUPERADMIN',
        ]);

        $response = $this->actingAs($admin)->get('/customers');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_customer_list(): void
    {
        $customer = Customer::create([
            'nama_perusahaan' => 'PT Test',
            'status' => 'ACTIVE',
        ]);

        $user = User::create([
            'username' => 'testcustomer',
            'password' => bcrypt('password123'),
            'role' => 'CUSTOMER',
            'customer_id' => $customer->id,
        ]);

        $response = $this->actingAs($user)->get('/customers');
        $response->assertStatus(403);
    }
}
