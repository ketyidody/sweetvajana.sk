<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AdminTranslationController extends Controller
{
    public function index(Request $request)
    {
        $query = Translation::query();

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%");
            });
        }

        $translations = $query->orderBy('group')->orderBy('key')->get();

        // Group by group.key, then by locale
        $grouped = [];
        foreach ($translations as $t) {
            $grouped[$t->group][$t->key][$t->locale] = $t->value;
        }

        $groups = Translation::select('group')->distinct()->orderBy('group')->pluck('group');

        return Inertia::render('Admin/Translations/Index', [
            'translations' => $grouped,
            'groups' => $groups,
            'languages' => Language::getActive(),
            'filters' => [
                'group' => $request->group,
                'search' => $request->search,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*.group' => 'required|string|max:50',
            'translations.*.key' => 'required|string|max:255',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.value' => 'nullable|string',
        ]);

        $localesToClear = [];

        foreach ($request->translations as $item) {
            Translation::updateOrCreate(
                [
                    'locale' => $item['locale'],
                    'group' => $item['group'],
                    'key' => $item['key'],
                ],
                ['value' => $item['value'] ?? '']
            );

            $localesToClear[$item['locale']] = true;
        }

        foreach (array_keys($localesToClear) as $locale) {
            Cache::forget("translations.{$locale}");
        }

        return back()->with('success', 'Translations updated.');
    }
}
