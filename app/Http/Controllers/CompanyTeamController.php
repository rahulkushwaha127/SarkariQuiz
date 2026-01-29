<?php

namespace App\Http\Controllers;

use App\Models\CompanyTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class CompanyTeamController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam(); // company represented by teams table
        abort_if(!$company, 404);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        
        $search = $request->string('q')->toString();
        abort_unless($auth->can('teams.view'), 403);
        
        // Users with teams.view can see teams they're part of
        // If they have teams.create/update/delete, they can see all teams
        $canManage = $auth->can('teams.create') || $auth->can('teams.update') || $auth->can('teams.delete');
        
        $teamsQuery = CompanyTeam::where('team_id', $company->id);
        
        if (!$canManage) {
            // Filter to only teams where user is a member
            $teamsQuery->whereHas('users', function ($q) use ($auth) {
                $q->where('users.id', $auth->id);
            });
        }
        
        $teams = $teamsQuery
            ->when($search, fn($q) => $q->where('name','like',"%{$search}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
        
        if ($request->boolean('partial')) {
            return view('team.teams._list_content', [
                'companyTeams' => $teams,
                'view' => $request->string('view','list')->toString(),
            ]);
        }
        
        return view('team.teams.index', [
            'companyTeams' => $teams,
            'companyUsers' => $company->users()->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        abort_unless($auth->can('teams.create'), 403);

        return view('team.teams._form', [
            'title' => __('Create Team'),
            'action' => route('company.teams.store'),
            'method' => 'POST',
            'companyUsers' => $company->users()->orderBy('name')->get(),
            'team' => new CompanyTeam(['team_id' => $company->id]),
            'selectedUsers' => [],
        ]);
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        abort_unless($auth->can('teams.create'), 403);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'users' => ['array'],
            'users.*' => ['integer'],
        ]);

        $team = CompanyTeam::create([
            'team_id' => $company->id,
            'name' => $data['name'],
        ]);
        $team->users()->sync($data['users'] ?? []);

        return redirect()->route('company.teams.index')->with('status', __('Team created.'));
    }

    public function edit(CompanyTeam $companyTeam)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        abort_unless($auth->can('teams.update'), 403);
        abort_unless($companyTeam->team_id === $company->id, 403);

        return view('team.teams._form', [
            'title' => __('Edit Team'),
            'action' => route('company.teams.update', $companyTeam),
            'method' => 'PUT',
            'companyUsers' => $company->users()->orderBy('name')->get(),
            'team' => $companyTeam,
            'selectedUsers' => $companyTeam->users()->pluck('users.id')->all(),
        ]);
    }

    public function update(Request $request, CompanyTeam $companyTeam)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        abort_unless($auth->can('teams.update'), 403);
        abort_unless($companyTeam->team_id === $company->id, 403);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'users' => ['array'],
            'users.*' => ['integer'],
        ]);

        $companyTeam->update(['name' => $data['name']]);
        $companyTeam->users()->sync($data['users'] ?? []);

        return redirect()->route('company.teams.index')->with('status', __('Team updated.'));
    }

    public function destroy(CompanyTeam $companyTeam)
    {
        $auth = Auth::user();
        $company = $auth->currentTeam();
        abort_if(!$company, 404);
        app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
        abort_unless($auth->can('teams.delete'), 403);
        abort_unless($companyTeam->team_id === $company->id, 403);

        $companyTeam->delete();
        return redirect()->route('company.teams.index')->with('status', __('Team deleted.'));
    }
}


