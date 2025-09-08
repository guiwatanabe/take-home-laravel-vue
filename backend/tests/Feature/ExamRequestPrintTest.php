<?php

namespace Tests\Feature;

use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamRequestsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_print_returns_pdf_response()
    {
        $exam = Exam::factory()->create(['id' => 1]);

        Pdf::shouldReceive('loadView')
            ->once()
            ->andReturnSelf();
        Pdf::shouldReceive('output')
            ->once()
            ->andReturn('PDF_CONTENT');

        $requestData = [
            [
                'id' => 0,
                'name' => 'Exames Avulsos',
                'exams' => [
                    $exam,
                ],
                'observations' => 'Teste',
            ],
        ];

        $response = $this->postJson('/api/print', $requestData);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'inline; filename="document.pdf"');
        $response->assertSee('PDF_CONTENT');
    }

    public function test_print_returns_validation_error()
    {
        $requestData = [
            [
                'id' => 0,
                'name' => 'Exames Avulsos',
                'exams' => [['id' => 0]],
                'observations' => 'Teste',
            ],
        ];

        $response = $this->postJson('/api/print', $requestData);

        $response->assertStatus(422);
    }
}
