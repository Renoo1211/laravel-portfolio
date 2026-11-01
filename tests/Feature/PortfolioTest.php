<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    public function test_root_redirects_to_indonesian(): void
    {
        $this->get('/')->assertRedirect('/id');
    }

    public function test_indonesian_page_renders_translated_content(): void
    {
        $this->get('/id')
            ->assertOk()
            ->assertSee('Mengolah data mentah')
            ->assertSee('Dashboard Transaksi Virtual')
            ->assertSee('Selesai')
            ->assertDontSee('Phising');
    }

    public function test_english_page_renders_translated_content(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('Turning raw data')
            ->assertSee('Virtual Transaction Dashboard')
            ->assertSee('Completed');
    }

    public function test_unknown_locale_is_not_found(): void
    {
        $this->get('/fr')->assertNotFound();
    }

    public function test_contact_form_sends_mail(): void
    {
        Mail::fake();

        $this->postJson('/contact', ['name' => 'Budi', 'email' => 'budi@example.com', 'message' => 'Halo'])
            ->assertOk()->assertJson(['ok' => true]);

        Mail::assertSent(ContactMessage::class);
    }

    public function test_contact_form_validates_input(): void
    {
        $this->postJson('/contact', ['name' => '', 'email' => 'bukan-email', 'message' => ''])
            ->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_honeypot_silently_drops_spam(): void
    {
        Mail::fake();

        $this->postJson('/contact', ['name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'spam', 'website' => 'http://spam'])
            ->assertOk();

        Mail::assertNothingSent();
    }
}
