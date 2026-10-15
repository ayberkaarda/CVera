<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class CveraTest extends TestCase
{
    use RefreshDatabase;

    private string $cv = 'Deniz Yılmaz deniz@example.com +90 555 123 45 67 Profesyonel Özet: React geliştirici. Deneyim: 12 proje geliştirdim, hızı %35 artırdım. Eğitim: Üniversite lisans. Beceriler: React, SQL, Git, PHP. Projeler: 5000 kullanıcı için uygulama tasarladım.';

    public function test_public_pages_are_available_without_account(): void
    {
        foreach (['/', '/analiz', '/ilana-gore', '/cv-olustur', '/sablonlar', '/on-yazi', '/portfolyo', '/rehber', '/eklenti', '/hesabim', '/asistan'] as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_analysis_is_explained_and_has_no_database_write(): void
    {
        $this->postJson('/api/analyze', ['cv' => $this->cv])->assertOk()->assertJsonStructure(['score', 'checks', 'recommendations', 'note'])->assertJsonPath('method', 'rule-based-v1');
        $this->assertDatabaseCount('documents', 0);
    }

    public function test_bad_input_is_rejected(): void
    {
        $this->postJson('/api/analyze', ['cv' => 'short'])->assertUnprocessable();
        $this->postJson('/api/analyze', ['cv' => $this->cv, 'job' => 'short'])->assertUnprocessable();
    }

    public function test_job_match_identifies_missing_keywords_without_substring_matches(): void
    {
        $r = $this->postJson('/api/analyze', ['cv' => $this->cv, 'job' => 'React, Docker, Java ve JavaScript deneyimi gereklidir.']);
        $r->assertOk()->assertJsonPath('matched', ['React'])->assertJsonPath('missing', ['JavaScript', 'Java', 'Docker']);
    }

    public function test_unsupported_job_does_not_claim_a_match_score(): void
    {
        $this->postJson('/api/analyze', ['cv' => $this->cv, 'job' => 'Sabah saatlerinde açılacak çiçekçi dükkanımız için personel arıyoruz.'])->assertOk()->assertJsonPath('matchScore', null);
    }

    public function test_guest_cannot_read_or_save_cloud_documents(): void
    {
        $this->getJson('/api/documents')->assertUnauthorized();
        $this->postJson('/api/documents', ['kind' => 'cv', 'title' => 'CV', 'content' => ['name' => 'Deniz']])->assertUnauthorized();
    }

    public function test_document_versions_restore_and_owner_isolation(): void
    {
        $one = User::factory()->create();
        $two = User::factory()->create();
        $id = $this->actingAs($one)->postJson('/api/documents', ['kind' => 'cv', 'title' => 'First', 'content' => ['name' => 'Deniz']])->assertOk()->json('id');
        $this->putJson('/api/documents/'.$id, ['kind' => 'cv', 'title' => 'Updated', 'content' => ['name' => 'Deniz Yılmaz']])->assertOk();
        $version = $this->getJson('/api/documents/'.$id.'/versions')->assertOk()->json('0.id');
        $this->postJson('/api/documents/'.$id.'/versions/'.$version)->assertOk()->assertJsonPath('title', 'First');
        $this->actingAs($two)->putJson('/api/documents/'.$id, ['kind' => 'cv', 'title' => 'Attack', 'content' => ['name' => 'Other']])->assertNotFound();
        $this->getJson('/api/documents/'.$id.'/versions')->assertNotFound();
        $this->deleteJson('/api/documents/'.$id)->assertNotFound();
    }

    public function test_portfolio_publication_is_explicit_and_reversible(): void
    {
        $u = User::factory()->create();
        $id = $this->actingAs($u)->postJson('/api/documents', ['kind' => 'portfolio', 'title' => 'Portfolio', 'content' => ['name' => '<script>alert(1)</script>', 'summary' => 'Test']])->json('id');
        $url = $this->postJson('/api/documents/'.$id.'/share', ['published' => true])->assertOk()->json('url');
        $this->get($url)->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert', false);
        $this->postJson('/api/documents/'.$id.'/share', ['published' => false])->assertOk();
        $this->get($url)->assertNotFound();
    }

    public function test_ai_unconfigured_is_honest_and_public(): void
    {
        config(['cvera.ai_key' => null]);
        $this->postJson('/api/ai', ['action' => 'summary', 'cv' => $this->cv])->assertStatus(503);
    }

    public function test_ai_response_uses_mock_provider_and_requires_review(): void
    {
        config(['cvera.ai_key' => 'test-key']);
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => 'Gerçek verilere dayalı özet.']]]])]);
        $this->postJson('/api/ai', ['action' => 'summary', 'cv' => $this->cv])->assertOk()->assertJsonPath('reviewRequired', true);
        Http::assertSent(fn ($r) => $r['messages'][0]['role'] === 'system' && $r['model'] === config('cvera.ai_model'));
    }

    public function test_analyze_requests_do_not_consume_ai_rate_limit(): void
    {
        config(['cvera.ai_key' => null]);
        for ($i = 0; $i < 11; $i++) {
            $this->postJson('/api/analyze', ['cv' => $this->cv])->assertOk();
        }$this->postJson('/api/ai', ['action' => 'summary', 'cv' => $this->cv])->assertStatus(503);
    }

    public function test_registration_login_and_logout(): void
    {
        $this->postJson('/api/register', ['name' => 'Deniz', 'email' => 'deniz@example.com', 'password' => 'ExamplePass123', 'password_confirmation' => 'ExamplePass123'])->assertOk()->assertJsonPath('user.name', 'Deniz');
        $this->postJson('/api/logout')->assertOk();
        $this->postJson('/api/login', ['email' => 'deniz@example.com', 'password' => 'wrong'])->assertUnprocessable();
        $this->postJson('/api/login',['email' => 'deniz@example.com', 'password' => 'ExamplePass123'])->assertOk();
    }
}
