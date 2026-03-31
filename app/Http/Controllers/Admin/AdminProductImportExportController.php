<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProductImportExportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        $filter = $request->query('filter', 'all');

        $query = Product::with(['category', 'sizes'])->latest();

        if ($filter === 'not_orderable_online') {
            $query->where('is_orderable_online', false);
        }

        $products = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products-export-'.now()->format('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'id',
                'name',
                'slug',
                'category',
                'description',
                'price',
                'is_active',
                'is_featured',
                'is_orderable_online',
                'is_available_for_collection',
                'soonest_availability',
                'sizes',
            ]);

            foreach ($products as $product) {
                $sizes = $product->sizes->map(fn ($size) => $size->name.':'.$size->pivot->price)->implode('|');

                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->slug,
                    $product->category?->name ?? '',
                    $product->description,
                    $product->price,
                    $product->is_active ? 1 : 0,
                    $product->is_featured ? 1 : 0,
                    $product->is_orderable_online ? 1 : 0,
                    $product->is_available_for_collection ? 1 : 0,
                    $product->soonest_availability ?? '',
                    $sizes,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Auto-detect delimiter: read first line and check for semicolons (Excel EU locale)
        $firstLine = fgets($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3); // skip BOM again after rewind
        }

        $header = fgetcsv($handle, 0, $delimiter);
        $header = array_map('trim', $header ?? []);

        if (! $header) {
            return back()->withErrors(['file' => 'The file is empty or invalid.']);
        }

        $requiredColumns = ['id', 'name', 'price', 'sizes'];
        $missing = array_diff($requiredColumns, $header);
        if ($missing) {
            return back()->withErrors(['file' => 'Missing columns: '.implode(', ', $missing)]);
        }

        $colIndex = array_flip($header);

        $updated = 0;
        $errors = [];

        DB::transaction(function () use ($handle, $colIndex, $delimiter, &$updated, &$errors) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) < count($colIndex)) {
                    continue;
                }

                $id = trim($row[$colIndex['id']] ?? '');
                if (! $id || ! is_numeric($id)) {
                    continue;
                }

                $product = Product::find((int) $id);
                if (! $product) {
                    $errors[] = "Product ID {$id} not found — skipped.";
                    continue;
                }

                $updateData = [];

                if (isset($colIndex['name']) && trim($row[$colIndex['name']]) !== '') {
                    $updateData['name'] = trim($row[$colIndex['name']]);
                }
                if (isset($colIndex['description'])) {
                    $updateData['description'] = trim($row[$colIndex['description']]);
                }
                if (isset($colIndex['price']) && is_numeric(trim($row[$colIndex['price']]))) {
                    $updateData['price'] = (float) trim($row[$colIndex['price']]);
                }
                if (isset($colIndex['is_active'])) {
                    $updateData['is_active'] = (bool) (int) trim($row[$colIndex['is_active']]);
                }
                if (isset($colIndex['is_featured'])) {
                    $updateData['is_featured'] = (bool) (int) trim($row[$colIndex['is_featured']]);
                }
                if (isset($colIndex['is_orderable_online'])) {
                    $updateData['is_orderable_online'] = (bool) (int) trim($row[$colIndex['is_orderable_online']]);
                }
                if (isset($colIndex['is_available_for_collection'])) {
                    $updateData['is_available_for_collection'] = (bool) (int) trim($row[$colIndex['is_available_for_collection']]);
                }
                if (isset($colIndex['soonest_availability'])) {
                    $val = trim($row[$colIndex['soonest_availability']]);
                    $updateData['soonest_availability'] = $val !== '' && is_numeric($val) ? (int) $val : null;
                }

                if ($updateData) {
                    $product->update($updateData);
                }

                // Sync sizes
                if (isset($colIndex['sizes'])) {
                    $sizesRaw = trim($row[$colIndex['sizes']]);
                    $syncData = [];

                    if ($sizesRaw !== '') {
                        foreach (explode('|', $sizesRaw) as $pair) {
                            $parts = explode(':', $pair, 2);
                            if (count($parts) !== 2) {
                                continue;
                            }
                            [$sizeName, $sizePrice] = $parts;
                            $sizeName = trim($sizeName);
                            $sizePrice = trim($sizePrice);

                            if ($sizeName === '' || ! is_numeric($sizePrice)) {
                                continue;
                            }

                            $size = Size::firstOrCreate(['name' => $sizeName]);
                            $syncData[$size->id] = ['price' => (float) $sizePrice];
                        }
                    }

                    $product->sizes()->sync($syncData);
                }

                $updated++;
            }
        });

        fclose($handle);

        $message = "Successfully updated {$updated} product(s).";
        if ($errors) {
            $message .= ' Skipped: '.implode(' ', $errors);
        }

        return back()->with('success', $message);
    }
}
