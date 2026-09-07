<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Step;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class StepController extends Controller
{
    public function update(Step $step): RedirectResponse
    {
        Gate::authorize('update', $step);

        $step->completed = ! $step->completed;
        $step->save();

        return back()->with('success', 'Step status updated.');
    }
}
