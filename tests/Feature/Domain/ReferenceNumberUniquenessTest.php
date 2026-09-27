<?php

namespace Tests\Feature\Domain;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceNumberUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_numbers_are_automatically_generated_and_unique(): void
    {
        $client = User::create([
            'name' => 'Ref Client',
            'email' => 'refclient@example.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $lead1 = Lead::create(['name' => 'Lead One', 'email' => 'lead1@example.com']);
        $lead2 = Lead::create(['name' => 'Lead Two', 'email' => 'lead2@example.com']);

        $this->assertEquals('STZ-LEAD-000001', $lead1->reference_number);
        $this->assertEquals('STZ-LEAD-000002', $lead2->reference_number);

        $proj1 = Project::create(['client_id' => $client->id, 'title' => 'Project 1']);
        $proj2 = Project::create(['client_id' => $client->id, 'title' => 'Project 2']);

        $this->assertEquals('STZ-PROJ-000001', $proj1->reference_number);
        $this->assertEquals('STZ-PROJ-000002', $proj2->reference_number);

        $quo = Quotation::create([
            'client_id' => $client->id,
            'issue_date' => now(),
            'valid_until' => now()->addDays(7),
        ]);
        $this->assertEquals('STZ-QUO-000001', $quo->reference_number);

        $inv = Invoice::create([
            'client_id' => $client->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
        ]);
        $this->assertEquals('STZ-INV-000001', $inv->reference_number);

        $pay = Payment::create([
            'client_id' => $client->id,
            'amount' => 1000.00,
        ]);
        $this->assertEquals('STZ-PAY-000001', $pay->reference_number);

        $doc = Document::create([
            'client_id' => $client->id,
            'uploaded_by_id' => $client->id,
            'original_filename' => 'file.pdf',
            'storage_path' => 'path/file.pdf',
        ]);
        $this->assertEquals('STZ-DOC-000001', $doc->reference_number);
    }
}
