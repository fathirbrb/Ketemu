<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FoundItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FoundReportTest extends TestCase
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

    private function report(User $user, array $overrides = []): FoundItem
    {
        return FoundItem::create($this->data($overrides) + [
            'user_id' => $user->id,
            'status' => 'tersimpan',
        ]);
    }

    public function test_visitors_can_view_list_detail_and_empty_results(): void
    {
        $this->get(route('found.index'))->assertOk()->assertSee('Belum ada laporan yang cocok');
        $item = $this->report(User::factory()->create());
        $this->get(route('found.index'))->assertOk()->assertSee($item->title);
        $this->get(route('found.show', $item))->assertOk()->assertSee($item->description)->assertDontSee('Hapus laporan');
        $this->get(route('found.show', 999999))->assertNotFound();
    }

    public function test_search_category_status_and_ownership_filters(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $first = $this->report($owner);
        $second = $this->report($other, [
            'title' => 'Kunci motor merah', 'location' => 'Parkiran',
            'category_id' => Category::create(['name' => 'Kunci'])->id,
            'status' => 'diklaim',
        ]);
        foreach ([['q' => 'DOMPET'], ['q' => 'perpustakaan'], ['category' => $first->category_id], ['status' => 'tersimpan']] as $filter) {
            $this->get(route('found.index', $filter))->assertOk()
                ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$first->id]);
        }
        $this->get(route('found.index', ['status' => 'diklaim']))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$second->id]);
        $this->actingAs($owner)->get(route('found.index', ['mine' => 1]))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$first->id]);
    }

    public function test_owner_can_create_edit_update_and_delete_with_photos(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($owner)->get(route('found.create'))->assertOk();
        $this->post(route('found.store'), $this->data([
            'photo' => UploadedFile::fake()->image('dompet.jpg'),
            'user_id' => $other->id, 'status' => 'diklaim',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $item = FoundItem::firstOrFail();
        $this->assertSame($owner->id, $item->user_id);
        $this->assertSame('tersimpan', $item->status);
        $oldPhoto = $item->photo;
        Storage::disk('public')->assertExists($oldPhoto);
        $this->get(route('found.edit', $item))->assertOk();
        $this->get(route('found.show', $item))->assertOk()->assertSee('Hapus laporan');
        $this->put(route('found.update', $item), $this->data([
            'title' => 'Dompet diklaim', 'status' => 'diklaim',
            'photo' => UploadedFile::fake()->image('baru.png'),
        ]))->assertSessionHasNoErrors()->assertRedirect(route('found.show', $item));
        $item->refresh();
        $this->assertSame('diklaim', $item->status);
        Storage::disk('public')->assertMissing($oldPhoto);
        $newPhoto = $item->photo;
        Storage::disk('public')->assertExists($newPhoto);
        $this->delete(route('found.destroy', $item))->assertRedirect(route('found.index'));
        $this->assertDatabaseMissing('found_items', ['id' => $item->id]);
        Storage::disk('public')->assertMissing($newPhoto);
    }

    public function test_owner_can_remove_photo_without_replacing_it(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        Storage::disk('public')->put('found/old.jpg', 'photo');
        $item = $this->report($owner, ['photo' => 'found/old.jpg']);
        $this->actingAs($owner)->put(route('found.update', $item), $this->data([
            'status' => 'tersimpan', 'remove_photo' => 1,
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($item->fresh()->photo);
        Storage::disk('public')->assertMissing('found/old.jpg');
    }

    public function test_another_user_cannot_edit_update_or_delete_report(): void
    {
        $item = $this->report(User::factory()->create());
        $this->actingAs(User::factory()->create());
        $this->get(route('found.edit', $item))->assertForbidden();
        $this->put(route('found.update', $item), $this->data(['status' => 'diklaim']))->assertForbidden();
        $this->delete(route('found.destroy', $item))->assertForbidden();
        $this->assertDatabaseHas('found_items', ['id' => $item->id, 'status' => 'tersimpan']);
    }

    public function test_guests_cannot_write_reports(): void
    {
        $item = $this->report(User::factory()->create());
        $this->getJson(route('found.create'))->assertUnauthorized();
        $this->postJson(route('found.store'), $this->data())->assertUnauthorized();
        $this->getJson(route('found.edit', $item))->assertUnauthorized();
        $this->putJson(route('found.update', $item), [])->assertUnauthorized();
        $this->deleteJson(route('found.destroy', $item))->assertUnauthorized();
        $this->assertDatabaseCount('found_items', 1);
    }

    public function test_invalid_input_and_photos_are_rejected(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $this->actingAs($owner);
        $this->postJson(route('found.store'), $this->data([
            'title' => '', 'category_id' => 99999,
            'event_date' => now()->addDay()->toDateString(), 'contact' => 'abc',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['title', 'category_id', 'event_date', 'contact']);
        $this->post(route('found.store'), $this->data([
            'photo' => UploadedFile::fake()->image('big.jpg')->size(2049),
        ]))->assertSessionHasErrors('photo');
        $this->post(route('found.store'), $this->data([
            'photo' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ]))->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('found_items', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
        $item = $this->report($owner);
        $this->putJson(route('found.update', $item), $this->data(['status' => 'bebas']))
            ->assertUnprocessable()->assertJsonValidationErrors('status');
    }
}