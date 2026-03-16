<?php

namespace Tests\Feature;

use App\Models\Anime;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_it_logs_created_event()
    {
        $this->actingAs($this->admin);

        $anime = Anime::create([
            'title' => 'Test Anime',
            'slug' => 'test-anime',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'created',
            'auditable_id' => $anime->id,
            'auditable_type' => 'anime',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_it_logs_updated_event_with_changes()
    {
        $anime = Anime::create([
            'title' => 'Original Title Update',
            'slug' => 'original-title-update',
            'status' => 1,
        ]);

        $this->actingAs($this->admin);

        $anime->update(['title' => 'Updated Title']);

        $log = AuditLog::where('event', 'updated')
            ->where('auditable_type', 'anime')
            ->where('auditable_id', $anime->id)
            ->first();

        $this->assertNotNull($log, 'Audit log for update not found');
        $this->assertEquals('Original Title Update', $log->old_values['title']);
        $this->assertEquals('Updated Title', $log->new_values['title']);
    }

    public function test_it_does_not_log_update_if_nothing_changed()
    {
        $anime = Anime::create([
            'title' => 'No Change Title',
            'slug' => 'no-change-title',
            'status' => 1,
        ]);

        $this->actingAs($this->admin);

        $anime->update(['title' => 'No Change Title']); // Same value

        $this->assertDatabaseMissing('audit_logs', [
            'event' => 'updated',
            'auditable_type' => 'anime',
            'auditable_id' => $anime->id,
        ]);
    }

    public function test_it_logs_deleted_event()
    {
        $anime = Anime::create([
            'title' => 'To Be Deleted Task',
            'slug' => 'to-be-deleted-task',
            'status' => 1,
        ]);

        $this->actingAs($this->admin);

        $anime->delete();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'deleted',
            'auditable_type' => 'anime',
            'auditable_id' => $anime->id,
        ]);
    }
}


