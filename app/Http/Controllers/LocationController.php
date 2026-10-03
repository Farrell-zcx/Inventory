<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-master-data');
        $query = Location::latest();
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $locations = $query->paginate(10)->withQueryString();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        Gate::authorize('manage-master-data');
        return view('locations.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-master-data');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $location = Location::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'location' => $location]);
        }

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        Gate::authorize('manage-master-data');
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        Gate::authorize('manage-master-data');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        Gate::authorize('delete-master-data');
        if ($location->items()->count() > 0) {
            return redirect()->route('locations.index')->with('error', 'Cannot delete location because it has associated items.');
        }
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}
