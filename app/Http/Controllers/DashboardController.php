<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = $user->isAdmin() ? Project::query() : $user->projects();

        $total    = (clone $query)->count();
        $pending  = (clone $query)->pending()->count();
        $approved = (clone $query)->approved()->count();
        $rejected = (clone $query)->rejected()->count();

        $pct = fn(int $n) => $total > 0 ? round(($n / $total) * 100) : 0;

        $recent = (clone $query)->with('user')->latest()->take(5)->get();

        $chartData = Project::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->when(! $user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        return view('dashboard', compact(
            'total', 'pending', 'approved', 'rejected', 'recent', 'chartData', 'pct'
        ));
    }
}
