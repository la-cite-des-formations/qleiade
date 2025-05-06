<?php

use Api\Controllers\AuditController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Models\Audit;
use Models\QualityLabel;
use School\Manager\SchoolManager;
use Tests\TestCase;
use App\Http\Requests\AuditRequest;

class AuditControllerTest extends TestCase
{
    use WithFaker;

    public function testIndex()
    {
        // Mock SchoolManager instance
        $schoolManagerMock = $this->getMockBuilder(SchoolManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create a mock Request with necessary data
        $requestMock = Request::create('/index', 'GET', [
            'params' => [
                'extern' => true,
                'formations' => [1, 2, 3],
                'groups' => [],
                'students' => [],
                'since' => '2023-01-01',
                'until' => '2023-06-01'
            ]
        ]);

        // Create an instance of AuditController with the mock SchoolManager
        $auditController = new AuditController($schoolManagerMock);

        // Invoke the index method
        $response = $auditController->index($requestMock);

        // Assert response status code and JSON structure
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('audit_type', $json);
        $this->assertArrayHasKey('extern', $json);
        $this->assertArrayHasKey('periods', $json);
        $this->assertArrayHasKey('formations', $json);
        $this->assertArrayHasKey('groups', $json);
        $this->assertArrayHasKey('students', $json);
    }

    public function testResult()
    {
        // Mock SchoolManager instance
        $schoolManagerMock = $this->getMockBuilder(SchoolManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create a mock Request with necessary data
        $requestMock = Request::create('/result', 'GET', [
            'params' => [
                'quality_label' => 'Qualiopi',
                // Add other necessary data for the test
            ]
        ]);

        // Create an instance of AuditController with the mock SchoolManager
        $auditController = new AuditController($schoolManagerMock);

        // Invoke the result method
        $response = $auditController->result($requestMock);

        // Assert response status code and JSON structure
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $json = json_decode($response->getContent(), true);
        // Add assertions for the response JSON if needed
    }
    public function testValidateAuditWithNewAudit()
    {

        // Mock SchoolManager instance
        $schoolManagerMock = $this->getMockBuilder(SchoolManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create an instance of AuditController
        $auditController = new AuditController($schoolManagerMock);

        // Generate random data for the request
        $requestData = [
            'name' => $this->faker->name,
            'date' => $this->faker->date,
            'auditor' => $this->faker->name,
            'type' => ['label' => 'Type Label'],
            'comment' => $this->faker->text,
            'sample' => $this->faker->randomDigit,
            'validation' => $this->faker->boolean,
            'wealths' => $this->faker->randomElements(),
            'quality_label' => $this->faker->word,
        ];

        $request = new AuditRequest($requestData);

        // Mock the QualityLabel model
        $qualityLabelMock = Mockery::mock(QualityLabel::class);
        $qualityLabelMock->shouldReceive('where')->once()->with('label', '=', $request['quality_label'])->andReturnSelf();
        $qualityLabelMock->shouldReceive('get')->once()->andReturnSelf();

        // Mock the Audit model
        $auditMock = Mockery::mock(Audit::class);
        $auditMock->shouldReceive('fill')->once()->with(['content' => json_encode([$request])]);
        $auditMock->shouldReceive('qualityLabel->associate')->once()->with($qualityLabelMock);
        $auditMock->shouldReceive('save')->once();

        // Set up the bindings for app() function
        $this->app->instance(QualityLabel::class, $qualityLabelMock);
        $this->app->instance(Audit::class, $auditMock);

        // Invoke the validateAudit method
        $response = $auditController->validateAudit($request);

        // Assert the response
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('audit_id', $response->getData());
    }
}
