<?php

namespace Tests\Feature;

use App\Models\Guide;
use App\Models\GuideSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_mentions_pureorange_ownership(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Candidakuur.nl is onderdeel van PureOrange BV');
    }

    public function test_contact_guide_lists_pureorange_company_details(): void
    {
        $guide = Guide::query()->create([
            'slug' => 'contact',
            'title' => 'Contact en eigenaar',
            'summary' => 'Praktijk ARDRA in Haarlem. Candidakuur.nl is onderdeel van PureOrange BV.',
            'access' => 'public',
            'sort_order' => 1,
        ]);

        GuideSection::query()->create([
            'guide_id' => $guide->id,
            'heading' => 'Website',
            'body' => "Candidakuur.nl is onderdeel van PureOrange BV.\nDe Rekere 22, 1749 MX Warmenhuizen.\nKvK 58004319.\nBTW NL852830130B01.\nE-mail saviem@pureorange.nl.\nWebsite www.pureorange.nl.",
            'sort_order' => 0,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('guides.show', 'contact'))
            ->assertOk()
            ->assertSee('PureOrange BV')
            ->assertSee('De Rekere 22')
            ->assertSee('1749 MX Warmenhuizen')
            ->assertSee('KvK 58004319')
            ->assertSee('BTW NL852830130B01')
            ->assertSee('PureOrange BV.<br />', false)
            ->assertSee('De Rekere 22, 1749 MX Warmenhuizen.<br />', false);
    }
}
