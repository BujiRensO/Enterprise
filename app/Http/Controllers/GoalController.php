<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GoalController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderBy('status')
            ->orderBy('target_date')
            ->get();

        return view('goals.index', compact('goals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('goals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date|after:today',
            'type' => 'required|in:savings,debt_payoff,purchase,other',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        Goal::create($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Financial goal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        return view('goals.show', compact('goal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        return view('goals.edit', compact('goal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date',
            'type' => 'required|in:savings,debt_payoff,purchase,other',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Financial goal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Financial goal deleted successfully.');
    }

    public function updateProgress(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'current_amount' => 'required|numeric|min:0',
        ]);

        $goal->update($validated);

        if ($goal->current_amount >= $goal->target_amount) {
            $goal->update(['status' => 'completed']);
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Goal progress updated successfully.');
    }
}
