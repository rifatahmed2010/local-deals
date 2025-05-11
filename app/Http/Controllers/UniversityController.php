<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\University;

class UniversityController extends Controller
{
    // Display the list of universities
    public function index()
    {
        $universities = University::all();
        return view('universities.index', compact('universities'));
    }

    // Show the form to create a new university
    public function create()
    {
        return view('universities.create');
    }

    // Store a new university in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        University::create($request->only(['name']));
        return redirect()->route('universities.index')->with('success', 'University created successfully.');
    }

    // Show the form to edit an existing university
    public function edit(University $university)
    {
        return view('universities.edit', compact('university'));
    }

    // Update an existing university
    public function update(Request $request, University $university)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $university->update($request->only(['name']));
        return redirect()->route('universities.index')->with('success', 'University updated successfully.');
    }

    public function destroy(University $university)
    {
        $university->delete();
        return redirect()->route('universities.index')->with('success', 'University deleted successfully.');
    }
}
