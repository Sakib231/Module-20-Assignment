<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        // Sorting functionality
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        $contacts = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        return view('index', compact('contacts'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:contacts',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        Contact::create($request->all());

        return redirect()->route('contacts.index');
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('show', compact('contact'));
    }

    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        return view('edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $contact->update($request->all());

        return redirect()->route('contacts.index');
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('contacts.index');
    }
}

