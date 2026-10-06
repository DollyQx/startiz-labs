<?php

namespace Tests\Feature\Public;

use App\Models\Industry;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('STARTIZ LABS');
        $response->assertSee('One Place for Your Complete Digital Business Solution');
        $response->assertSee('Start Your Project');
        $response->assertSee('Client Login');
    }

    public function test_services_index_and_show_routes_render(): void
    {
        $service = Service::create([
            'name' => 'Custom Web Engineering',
            'slug' => 'custom-web-engineering',
            'short_description' => 'High-performance web apps built with Laravel.',
            'is_active' => true,
        ]);

        $responseIndex = $this->get('/services');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Custom Web Engineering');

        $responseShow = $this->get('/services/custom-web-engineering');
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Custom Web Engineering');
    }

    public function test_industries_index_and_show_routes_render(): void
    {
        $industry = Industry::create([
            'name' => 'Healthcare Systems',
            'slug' => 'healthcare-systems',
            'description' => 'Medical clinic software solutions.',
            'is_active' => true,
        ]);

        $responseIndex = $this->get('/industries');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Healthcare Systems');

        $responseShow = $this->get('/industries/healthcare-systems');
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Healthcare Systems');
    }

    public function test_portfolio_index_and_show_routes_render(): void
    {
        $responseIndex = $this->get('/portfolio');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Official Portfolio');

        $responseShow = $this->get('/portfolio/notes-study');
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Notes Study');
    }

    public function test_about_route_renders(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('About Startiz Labs');
    }

    public function test_contact_route_renders(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Contact Information');
    }

    public function test_start_project_route_renders(): void
    {
        $response = $this->get('/start-project');
        $response->assertStatus(200);
        $response->assertSee('Start Your Custom Software Project');
    }

    public function test_partners_landing_and_terms_routes_render(): void
    {
        $responseLanding = $this->get('/partners');
        $responseLanding->assertStatus(200);
        $responseLanding->assertSee('Earn 20% Commission by Referring Clients to Startiz Labs');
        $responseLanding->assertSee(route('partner.register'));

        $responseTerms = $this->get('/partner-terms');
        $responseTerms->assertStatus(200);
        $responseTerms->assertSee('Partner Program Terms & Conditions', false);
    }

    public function test_existing_auth_routes_remain_functional(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_healthz_route_returns_ok_response(): void
    {
        $response = $this->get('/healthz');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }

    public function test_https_url_generation_and_reverse_proxy_trust(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Port' => '443',
        ])->get('/');

        $response->assertStatus(200);
        $this->assertStringStartsWith('https://', asset('css/public.css'));
    }
}
