<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guides_and_menu_are_editable_in_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin/guides')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/guides/create')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/menu-days')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/menu-days/create')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/symptom-guides')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/symptom-guides/create')
            ->assertOk();
    }
}
