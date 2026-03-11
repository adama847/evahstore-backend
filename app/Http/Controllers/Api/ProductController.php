<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Paramètres optionnels : ?category=bracelet|bestseller|perruque
     */
    public function index(Request $request)
    {
        $query = Product::query()->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get());
    }

    /**
     * GET /api/products/{id}
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * POST /api/products  (admin uniquement)
     * Supporte multipart/form-data (fichier) ou JSON (url)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|string|max:100',
            'category'    => 'required|in:bracelet,bestseller,perruque',
            'badge'       => 'nullable|in:Nouveau,Promo',
            'description' => 'nullable|string',
            'image'       => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov|max:20480',
            'image_url'   => 'nullable|url',
        ]);

        $imagePath = $this->handleImage($request);

        $product = Product::create([
            'name'        => $validated['name'],
            'price'       => $validated['price'],
            'category'    => $validated['category'],
            'badge'       => $validated['badge'] ?? null,
            'description' => $validated['description'] ?? null,
            'image'       => $imagePath ?? $request->image_url,
            'is_video'    => $request->hasFile('image')
                                ? in_array($request->file('image')->extension(), ['mp4','mov'])
                                : str_ends_with($request->image_url ?? '', '.mp4'),
        ]);

        return response()->json($product, 201);
    }

    /**
     * PUT/PATCH /api/products/{id}  (admin uniquement)
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'price'       => 'sometimes|string|max:100',
            'category'    => 'sometimes|in:bracelet,bestseller,perruque',
            'badge'       => 'nullable|in:Nouveau,Promo',
            'description' => 'nullable|string',
            'image'       => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov|max:20480',
            'image_url'   => 'nullable|url',
        ]);

        // Nouvelle image uploadée → supprimer l'ancienne si locale
        if ($request->hasFile('image')) {
            $this->deleteOldImage($product);
            $validated['image']    = $this->handleImage($request);
            $validated['is_video'] = in_array($request->file('image')->extension(), ['mp4','mov']);
        } elseif ($request->filled('image_url')) {
            $this->deleteOldImage($product);
            $validated['image']    = $request->image_url;
            $validated['is_video'] = str_ends_with($request->image_url, '.mp4');
        }

        unset($validated['image_url']); // pas un champ de la table
        $product->update($validated);

        return response()->json($product->fresh());
    }

    /**
     * DELETE /api/products/{id}  (admin uniquement)
     */
    public function destroy(Product $product)
    {
        $this->deleteOldImage($product);
        $product->delete();

        return response()->json(['message' => 'Produit supprimé.']);
    }

    /**
     * GET /api/stats  (admin uniquement)
     */
    public function stats()
    {
        $byCategory = Product::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        return response()->json([
            'total'       => Product::count(),
            'byCategory'  => $byCategory,
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────

    private function handleImage(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('products', 'public');
        }
        return null;
    }

    private function deleteOldImage(Product $product): void
    {
        if ($product->image && !str_starts_with($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }
    }
}