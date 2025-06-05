<?php

namespace Modules\Transaction\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Transaction\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query();;

        $filterable = [
            'client_id',
            'frontline_liaison_officer_id',
            'main_case_handler_id',
            'financial_officer_id',
            'executive_director_id',
            'legal_supervisor_id',
            'quality_assurance_officer_id',
            'bank_liaison_officer_id',
            'current_status',
        ];

        foreach ($filterable as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        return $this->respondOk($query->paginate());
    }

     public function show($id)
    {
        $transaction = Transaction::with([
            'client',
            'frontlineLiaisonOfficer',
            'mainCaseHandler',
            'financialOfficer',
            'executiveDirector',
            'legalSupervisor',
            'qualityAssuranceOfficer',
            'bankLiaisonOfficer'
        ])->find($id);

        if (!$transaction) {
            return $this->respondNotFound(null, 'Transaction not found.');
        }

        return $this->respondOk($transaction);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'                     => 'required|exists:clients,id',
            'frontline_liaison_officer_id'  => 'nullable|exists:users,id',
            'main_case_handler_id'          => 'nullable|exists:users,id',
            'financial_officer_id'          => 'nullable|exists:users,id',
            'executive_director_id'         => 'nullable|exists:users,id',
            'legal_supervisor_id'           => 'nullable|exists:users,id',
            'quality_assurance_officer_id'  => 'nullable|exists:users,id',
            'bank_liaison_officer_id'       => 'nullable|exists:users,id',
            'current_status'                => 'nullable|in:Pending,In_Review,Approved,Rejected,On_Hold,Completed,Cancelled',
        ]);

        $validated['status_history'] = [
            [
                'status' => $validated['current_status'] ?? 'Pending',
                'changed_at' => now()->toDateTimeString()
            ]
        ];

        $transaction = Transaction::create($validated);

        return $this->respondCreated($transaction, 'Transaction created successfully.');
    }


    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return $this->respondNotFound(null, 'Transaction not found.');
        }

        $validated = $request->validate([
            'client_id'                     => 'nullable|exists:clients,id',
            'frontline_liaison_officer_id'  => 'nullable|exists:users,id',
            'main_case_handler_id'          => 'nullable|exists:users,id',
            'financial_officer_id'          => 'nullable|exists:users,id',
            'executive_director_id'         => 'nullable|exists:users,id',
            'legal_supervisor_id'           => 'nullable|exists:users,id',
            'quality_assurance_officer_id'  => 'nullable|exists:users,id',
            'bank_liaison_officer_id'       => 'nullable|exists:users,id',
            'current_status'                => 'nullable|in:Pending,In_Review,Approved,Rejected,On_Hold,Completed,Cancelled',
        ]);

        if (isset($validated['current_status']) && $validated['current_status'] !== $transaction->current_status) {
            $statusHistory = $transaction->status_history ?? [];
            $userId   = $request->user()->id;
            $roleName = optional(auth()->user()->roles()->first())->name ?? 'UnknownRole';
            $statusHistory[] = [
                'status'     => "{$roleName}_{$userId}_{$validated['current_status']}",
                'changed_at' => now()->toDateTimeString()
            ];
            $validated['status_history'] = $statusHistory;
        }

        $transaction->update($validated);

        return $this->respondOk($transaction, 'Transaction updated successfully.');
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return $this->respondNotFound(null, 'Transaction not found.');
        }

        $transaction->delete();

        return $this->respondOk(null, 'Transaction deleted successfully.');
    }
}
