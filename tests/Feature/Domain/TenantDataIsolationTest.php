<?php

namespace Tests\Feature\Domain;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_data_relationships_do_not_cross_link(): void
    {
        $clientA = User::create([
            'name' => 'Client A',
            'email' => 'clienta@isolation.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $clientB = User::create([
            'name' => 'Client B',
            'email' => 'clientb@isolation.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $projA = Project::create(['client_id' => $clientA->id, 'title' => 'Project of Client A']);
        $projB = Project::create(['client_id' => $clientB->id, 'title' => 'Project of Client B']);

        $quoA = Quotation::create(['client_id' => $clientA->id, 'issue_date' => now(), 'valid_until' => now()]);
        $quoB = Quotation::create(['client_id' => $clientB->id, 'issue_date' => now(), 'valid_until' => now()]);

        $invA = Invoice::create(['client_id' => $clientA->id, 'issue_date' => now(), 'due_date' => now()]);
        $invB = Invoice::create(['client_id' => $clientB->id, 'issue_date' => now(), 'due_date' => now()]);

        $payA = Payment::create(['client_id' => $clientA->id, 'amount' => 5000.00]);
        $payB = Payment::create(['client_id' => $clientB->id, 'amount' => 10000.00]);

        $docA = Document::create(['client_id' => $clientA->id, 'uploaded_by_id' => $clientA->id, 'original_filename' => 'a.pdf', 'storage_path' => 'a.pdf']);
        $docB = Document::create(['client_id' => $clientB->id, 'uploaded_by_id' => $clientB->id, 'original_filename' => 'b.pdf', 'storage_path' => 'b.pdf']);

        // Assert Client A relationships
        $this->assertTrue($clientA->projects->contains($projA));
        $this->assertFalse($clientA->projects->contains($projB));

        $this->assertTrue($clientA->quotations->contains($quoA));
        $this->assertFalse($clientA->quotations->contains($quoB));

        $this->assertTrue($clientA->invoices->contains($invA));
        $this->assertFalse($clientA->invoices->contains($invB));

        $this->assertTrue($clientA->payments->contains($payA));
        $this->assertFalse($clientA->payments->contains($payB));

        $this->assertTrue($clientA->documents->contains($docA));
        $this->assertFalse($clientA->documents->contains($docB));

        // Assert Client B relationships
        $this->assertTrue($clientB->projects->contains($projB));
        $this->assertFalse($clientB->projects->contains($projA));

        $this->assertTrue($clientB->quotations->contains($quoB));
        $this->assertFalse($clientB->quotations->contains($quoA));

        $this->assertTrue($clientB->invoices->contains($invB));
        $this->assertFalse($clientB->invoices->contains($invA));

        $this->assertTrue($clientB->payments->contains($payB));
        $this->assertFalse($clientB->payments->contains($payA));

        $this->assertTrue($clientB->documents->contains($docB));
        $this->assertFalse($clientB->documents->contains($docA));
    }
}
