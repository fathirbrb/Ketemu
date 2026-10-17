<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LostReportTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Dompet kulit cokelat',
            'category_id' => Category::firstOrCreate(['name' => 'Dompet'])->id,
            'description' => 'Dompet kecil dengan jahitan putih.',
            'location' => 'Perpustakaan',
            'event_date' => now()->subDay()->toDateString(),
            'contact' => '081234567890',
        ], $overrides);
    }

    private function report(User $user, array $overrides = []): LostItem
    {
        return LostItem::create($this->data($overrides) + [
            'user_id' => $user->id,
            'status' => 'hilang',
        ]);
    }

    public function test_visitors_can_view_list_detail_and_empty_results(): void
    {
        $this->get(route('lost.index'))->assertOk()->assertSee('Belum ada laporan yang cocok');
        $item = $this->report(User::factory()->create());
        $this->get(route('lost.index'))->assertOk()->assertSee($item->title);
        $this->get(route('lost.show', $item))->assertOk()->assertSee($item->description)->assertDontSee('Hapus laporan');
        $this->get(route('lost.show', 999999))->assertNotFound();
    }

    public function test_search_category_status_and_ownership_filters(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $first = $this->report($owner);
        $second = $this->report($other, [
            'title' => 'Kunci motor merah', 'location' => 'Parkiran',
            'category_id' => Category::create(['name' => 'Kunci'])->id,
            'status' => 'ditemukan',
        ]);
        foreach ([['q' => 'DOMPET'], ['q' => 'perpustakaan'], ['category' => $first->category_id], ['status' => 'hilang']] as $filter) {
            $this->get(route('lost.index', $filter))->assertOk()
                ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$first->id]);
        }
        $this->get(route('lost.index', ['status' => 'ditemukan']))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$second->id]);
        $this->actingAs($owner)->get(route('lost.index', ['mine' => 1]))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$first->id]);
    }

    public function test_owner_can_create_edit_update_and_delete_with_photos(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($owner)->get(route('lost.create'))->assertOk();
        $this->post(route('lost.store'), $this->data([
            'photo' => UploadedFile::fake()->image('dompet.jpg'),
            'user_id' => $other->id, 'status' => 'ditemukan',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $item = LostItem::firstOrFail();
        $this->assertSame($owner->id, $item->user_id);
        $this->assertSame('hilang', $item->status);
        $oldPhoto = $item->photo;
        Storage::disk('public')->assertExists($oldPhoto);
        $this->get(route('lost.edit', $item))->assertOk();
        $this->get(route('lost.show', $item))->assertOk()->assertSee('Hapus laporan');
        $this->put(route('lost.update', $item), $this->data([
            'title' => 'Dompet ditemukan', 'status' => 'ditemukan',
            'photo' => UploadedFile::fake()->image('baru.png'),
        ]))->assertSessionHasNoErrors()->assertRedirect(route('lost.show', $item));
        $item->refresh();
        $this->assertSame('ditemukan', $item->status);
        Storage::disk('public')->assertMissing($oldPhoto);
        $newPhoto = $item->photo;
        Storage::disk('public')->assertExists($newPhoto);
        $this->delete(route('lost.destroy', $item))->assertRedirect(route('lost.index'));
        $this->assertDatabaseMissing('lost_items', ['id' => $item->id]);
        Storage::disk('public')->assertMissing($newPhoto);
    }

    public function test_owner_can_remove_photo_without_replacing_it(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        Storage::disk('public')->put('lost/old.jpg', 'photo');
        $item = $this->report($owner, ['photo' => 'lost/old.jpg']);
        $this->actingAs($owner)->put(route('lost.update', $item), $this->data([
            'status' => 'hilang', 'remove_photo' => 1,
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($item->fresh()->photo);
        Storage::disk('public')->assertMissing('lost/old.jpg');
    }

    public function test_another_user_cannot_edit_update_or_delete_report(): void
    {
        $item = $this->report(User::factory()->create());
        $this->actingAs(User::factory()->create());
        $this->get(route('lost.edit', $item))->assertForbidden();
        $this->put(route('lost.update', $item), $this->data(['status' => 'ditemukan']))->assertForbidden();
        $this->delete(route('lost.destroy', $item))->assertForbidden();
        $this->assertDatabaseHas('lost_items', ['id' => $item->id, 'status' => 'hilang']);
    }

    public function test_guests_cannot_write_reports(): void
    {
        $item = $this->report(User::factory()->create());
        $this->getJson(route('lost.create'))->assertUnauthorized();
        $this->postJson(route('lost.store'), $this->data())->assertUnauthorized();
        $this->getJson(route('lost.edit', $item))->assertUnauthorized();
        $this->putJson(route('lost.update', $item), [])->assertUnauthorized();
        $this->deleteJson(route('lost.destroy', $item))->assertUnauthorized();
        $this->assertDatabaseCount('lost_items', 1);
    }

    public function test_invalid_input_and_photos_are_rejected(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $this->actingAs($owner);
        $this->postJson(route('lost.store'), $this->data([
            'title' => '', 'category_id' => 99999,
            'event_date' => now()->addDay()->toDateString(), 'contact' => 'abc',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['title', 'category_id', 'event_date', 'contact']);
        $this->post(route('lost.store'), $this->data([
            'photo' => UploadedFile::fake()->image('big.jpg')->size(2049),
        ]))->assertSessionHasErrors('photo');
        $this->post(route('lost.store'), $this->data([
            'photo' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ]))->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('lost_items', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
        $item = $this->report($owner);
        $this->putJson(route('lost.update', $item), $this->data(['status' => 'bebas']))
            ->assertUnprocessable()->assertJsonValidationErrors('status');
    }
}