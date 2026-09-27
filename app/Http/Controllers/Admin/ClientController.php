<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a paginated list of client accounts with search and status filtering.
     */
    public function index(Request $request): View
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');

        $query = User::where('role', UserRole::CLIENT->value)
            ->with('clientProfile');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('clientProfile', function ($qp) use ($search) {
                      $qp->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($status) && in_array($status, array_column(UserStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        $clients = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', compact('clients', 'search', 'status'));
    }

    /**
     * Display comprehensive details for a specific client.
     */
    public function show(User $client): View
    {
        // Enforce role check
        if (!$client->isClient()) {
            abort(404, 'Client not found.');
        }

        $client->load('clientProfile');
        $client->loadCount(['projects', 'leads', 'quotations', 'invoices']);

        ActivityLogger::log(
            action: 'client.viewed',
            subject: $client,
            description: "Viewed profile for client {$client->name} ({$client->email})"
        );

        return view('admin.clients.show', compact('client'));
    }
}
