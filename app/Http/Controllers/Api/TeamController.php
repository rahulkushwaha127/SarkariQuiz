<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Http\Resources\UserResource;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Get user's teams
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $teams = $user->teams()->with('owner')->get();
        $ownedTeams = $user->ownedTeams()->get();
        
        $allTeams = $teams->merge($ownedTeams)->unique('id');

        return response()->json([
            'success' => true,
            'data' => TeamResource::collection($allTeams),
        ]);
    }

    /**
     * Create a new team
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $team = Team::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'website' => $request->website,
            'is_active' => true,
        ]);

        // Add owner to team members
        $team->users()->attach($request->user()->id, [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully',
            'data' => new TeamResource($team->load('owner')),
        ], 201);
    }

    /**
     * Get team details
     */
    public function show(Request $request, Team $team)
    {
        // Check if user has access to this team
        $user = $request->user();
        if (!$team->hasMember($user) && $team->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new TeamResource($team->load('owner', 'users')),
        ]);
    }

    /**
     * Update team
     */
    public function update(Request $request, Team $team)
    {
        // Only owner or admin can update
        $user = $request->user();
        if (!$team->isOwnerOrAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $team->fill($request->only(['name', 'description', 'website', 'is_active']));
        
        if ($request->has('name')) {
            $team->slug = Str::slug($request->name);
        }
        
        $team->save();

        return response()->json([
            'success' => true,
            'message' => 'Team updated successfully',
            'data' => new TeamResource($team->fresh()->load('owner')),
        ]);
    }

    /**
     * Delete team
     */
    public function destroy(Request $request, Team $team)
    {
        // Only owner can delete
        if ($team->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the team owner can delete the team',
            ], 403);
        }

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully',
        ]);
    }

    /**
     * Get team members
     */
    public function members(Request $request, Team $team)
    {
        $user = $request->user();
        if (!$team->hasMember($user) && $team->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $members = $team->users()->withPivot('role', 'joined_at')->get();

        return response()->json([
            'success' => true,
            'data' => $members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->pivot->role,
                    'joined_at' => $member->pivot->joined_at,
                ];
            }),
        ]);
    }

    /**
     * Add member to team
     */
    public function addMember(Request $request, Team $team)
    {
        // Only owner or admin can add members
        $user = $request->user();
        if (!$team->isOwnerOrAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'string', 'in:owner,admin,member'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $member = \App\Models\User::findOrFail($request->user_id);

        if ($team->hasMember($member)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a member of this team',
            ], 422);
        }

        $team->users()->attach($member->id, [
            'role' => $request->role,
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member added successfully',
            'data' => new UserResource($member),
        ], 201);
    }

    /**
     * Remove member from team
     */
    public function removeMember(Request $request, Team $team, $user)
    {
        // Only owner or admin can remove members
        $currentUser = $request->user();
        if (!$team->isOwnerOrAdmin($currentUser)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $member = \App\Models\User::findOrFail($user);

        if (!$team->hasMember($member)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this team',
            ], 404);
        }

        // Cannot remove the owner
        if ($team->user_id === $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the team owner',
            ], 422);
        }

        $team->users()->detach($member->id);

        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully',
        ]);
    }
}

