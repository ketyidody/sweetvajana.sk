<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminLanguageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Languages/Index', [
            'languages' => Language::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Languages/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:5|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validated['is_default'] ?? false) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language created.');
    }

    public function edit(Language $language)
    {
        return Inertia::render('Admin/Languages/Form', [
            'language' => $language,
        ]);
    }

    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:5|unique:languages,code,'.$language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validated['is_default'] ?? false) {
            Language::where('is_default', true)->where('id', '!=', $language->id)->update(['is_default' => false]);
        }

        $language->update($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated.');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete the default language.');
        }

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language deleted.');
    }
}
