<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_all_packages_with_exams()
    {
        $package = Package::factory()->has(Exam::factory()->count(2))->create();

        $response = $this->getJson('/api/packages');

        $response->assertOk()
            ->assertJsonFragment(['id' => $package->id])
            ->assertJsonStructure(['data' => [['id', 'name', 'exams']]]);
    }

    public function test_it_shows_a_single_package_with_exams()
    {
        $package = Package::factory()->has(Exam::factory()->count(1))->create();

        $response = $this->getJson("/api/packages/{$package->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $package->id])
            ->assertJsonStructure(['data' => ['id', 'name', 'exams']]);
    }

    public function test_it_creates_a_package_with_exams()
    {
        $exams = Exam::factory()->count(2)->create();

        $payload = [
            'name' => 'Test Package',
            'observations' => 'Some observations',
            'exams' => $exams->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/packages', $payload);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Test Package'])
            ->assertJsonStructure([
                'id',
                'name',
                'exams' => [
                    '*' => ['id', 'name'],
                ],
            ]);

        $this->assertDatabaseHas('packages', ['name' => 'Test Package']);
        $this->assertDatabaseCount('exam_package', 2);
    }

    public function test_it_updates_a_package_and_syncs_exams()
    {
        $package = Package::factory()->has(Exam::factory()->count(1))->create();
        $newExams = Exam::factory()->count(2)->create();

        $payload = [
            'name' => 'Updated Package',
            'observations' => 'Updated observations',
            'exams' => $newExams->pluck('id')->toArray(),
        ];

        $response = $this->putJson("/api/packages/{$package->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Package'])
            ->assertJsonStructure([
                'id',
                'name',
                'exams' => [
                    '*' => ['id', 'name'],
                ],
            ]);

        $this->assertDatabaseHas('packages', ['name' => 'Updated Package']);
        $this->assertDatabaseCount('exam_package', 2);
    }

    public function test_it_deletes_a_package()
    {
        $package = Package::factory()->create();

        $response = $this->deleteJson("/api/packages/{$package->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Package deleted successfully.']);

        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
    }
}
