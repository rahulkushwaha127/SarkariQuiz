<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $search = $request->string('q')->toString();
        $view = $request->string('view', 'list')->toString();
        $teams = Team::query()
            ->with('owner')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if ($request->boolean('partial')) {
            return view('companies._list_content', [
                'teams' => $teams,
                'view' => $view,
            ]);
        }

        return view('companies.index', compact('teams'));
    }

    public function create()
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $team = new Team();
        return view('companies._form', [
            'team' => $team,
            'action' => route('companies.store'),
            'method' => 'POST',
            'title' => 'Create Company',
        ]);
    }

    public function store(Request $request, TeamService $teamService)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            'website' => ['nullable','url'],
            'owner_name' => ['nullable','string','max:255'],
            'owner_email' => ['required','email','max:255'],
            'owner_password' => ['required','string','min:8'],
        ]);

        // Create or resolve owner user
        $owner = \App\Models\User::where('email', $validated['owner_email'])->first();
        if (!$owner) {
            $owner = \App\Models\User::create([
                'name' => $validated['owner_name'] ?: ($validated['name'].' Owner'),
                'email' => $validated['owner_email'],
                'password' => bcrypt($validated['owner_password']),
            ]);
        }

        $teamData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'website' => $validated['website'] ?? null,
        ];
        $team = $teamService->create($teamData, $owner);
        return redirect()->route('companies.index')->with('status', 'Company created.');
    }

    public function edit(Team $company)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $company->load('owner');
        return view('companies._form', [
            'team' => $company,
            'action' => route('companies.update', $company),
            'method' => 'PUT',
            'title' => 'Edit Company',
        ]);
    }

    public function update(Request $request, Team $company, TeamService $teamService)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            'website' => ['nullable','url'],
            'is_active' => ['nullable','boolean'],
            'owner_name' => ['nullable','string','max:255'],
            'owner_email' => ['nullable','email','max:255'],
            'owner_password' => ['nullable','string','min:8'],
        ]);

        // Update owner if provided
        if (!empty($validated['owner_email']) || !empty($validated['owner_name']) || !empty($validated['owner_password'])) {
            $owner = $company->owner;
            if (!$owner) {
                // Create owner if doesn't exist
                $owner = \App\Models\User::create([
                    'name' => $validated['owner_name'] ?: ($validated['name'].' Owner'),
                    'email' => $validated['owner_email'] ?: ($validated['name'].'@example.com'),
                    'password' => bcrypt($validated['owner_password'] ?: str()->random(12)),
                ]);
                $company->user_id = $owner->id;
                $company->save();
            } else {
                // Update existing owner
                $updateData = [];
                if (!empty($validated['owner_name'])) {
                    $updateData['name'] = $validated['owner_name'];
                }
                if (!empty($validated['owner_email'])) {
                    // Check if email is already taken by another user
                    $existing = \App\Models\User::where('email', $validated['owner_email'])->where('id', '!=', $owner->id)->first();
                    if (!$existing) {
                        $updateData['email'] = $validated['owner_email'];
                    }
                }
                if (!empty($validated['owner_password'])) {
                    $updateData['password'] = bcrypt($validated['owner_password']);
                }
                if (!empty($updateData)) {
                    $owner->update($updateData);
                }
            }
        }

        $teamService->update($company, $validated);
        return redirect()->route('companies.index')->with('status', 'Company updated.');
    }

    public function destroy(Team $company, TeamService $teamService)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $teamService->delete($company);
        return redirect()->route('companies.index')->with('status', 'Company deleted.');
    }

    public function toggleActive(Team $company)
    {
        abort_unless(Auth::user()->is_super_admin, 403);
        $company->is_active = !$company->is_active;
        $company->save();
        return redirect()->back()->with('status', $company->is_active ? __('Company enabled.') : __('Company disabled.'));
    }
}


