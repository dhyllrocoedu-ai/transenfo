<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        $teams = Team::with(['leader', 'members'])->latest()->get();

        return view('teams.index', compact('teams'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('teams.create', [
            'leaders' => User::query()->whereIn('role', ['super_admin', 'administrator', 'enforcer'])->orderBy('name')->get(),
            'members' => User::query()->whereIn('role', ['super_admin', 'administrator', 'enforcer'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $team = Team::create($data);
        $team->members()->sync($data['members'] ?? []);

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function edit(Team $team): View
    {
        $this->authorizeAdmin();

        return view('teams.edit', [
            'team' => $team->load(['members']),
            'leaders' => User::query()->whereIn('role', ['super_admin', 'administrator', 'enforcer'])->orderBy('name')->get(),
            'members' => User::query()->whereIn('role', ['super_admin', 'administrator', 'enforcer'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $team->update($data);
        $team->members()->sync($data['members'] ?? []);

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
