<?php

namespace Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Client\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('national_id')) {
            $query->where('national_id', 'like', '%' . $request->national_id . '%');
        }
    
        if ($request->filled('nationality')) {
            $query->where('nationality', 'like', '%' . $request->nationality . '%');
        }

        if ($request->filled('religion')) {
            $query->where('religion', 'like', '%' . $request->religion . '%');
        }

        if ($request->filled('job')) {
            $query->where('job', 'like', '%' . $request->job . '%');
        }

        if ($request->filled('financing_type')) {
            $query->where('financing_type', $request->financing_type);
        }

        return $this->respondOk($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'nullable|string',
            'financing_type' => 'required|in:Personal_Financing,Real_Estate_Financing,Car_Financing',
            'job' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'work_nature' => 'nullable|string',
            'nationality' => 'nullable|string',
            'other_income_sources' => 'nullable|string',
            'religion' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'national_id' => 'nullable|string',
            'has_previous_loan' => 'required|boolean',
            'previous_loan_name' => 'required_if:has_previous_loan,1|string|nullable',
            'previous_loan_value' => 'required_if:has_previous_loan,1|numeric|nullable',
        ]);

        $client = Client::create($validated);

        return $this->respondCreated($client, 'Client created successfully.');
    }

    public function show($id)
    {
        $client = Client::with('user')->find($id);
        if(!$client)
        {
            return $this->respondNotFound(null,'Client not found.');
        }
        return $this->respondOk($client);
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if(!$client)
        {
            return $this->respondNotFound(null,'Client not found.');
        }

        $validated = $request->validate([
            'address' => 'nullable|string',
            'financing_type' => 'nullable|in:Personal_Financing,Real_Estate_Financing,Car_Financing',
            'job' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'work_nature' => 'nullable|string',
            'nationality' => 'nullable|string',
            'other_income_sources' => 'nullable|string',
            'religion' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'national_id' => 'nullable|string',
            'has_previous_loan' => 'boolean',
            'previous_loan_name' => 'nullable|string',
            'previous_loan_value' => 'nullable|numeric',
        ]);

        $client->update($validated);

        return $this->respondOk($client, 'Client updated successfully.');
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if(!$client)
        {
            return $this->respondNotFound(null,'Client not found.');
        }
        $client->delete();

        return $this->respondOk(null,'Client deleted successfully.');
    }
}
