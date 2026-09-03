<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get paginated list of products with filters.
     *
     * @param int|null $categoryId
     * @param int|null $brandId
     * @param string|null $search
     * @param string|null $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(
        ?int $categoryId = null,
        ?int $brandId = null,
        ?string $search = null,
        ?string $sort = 'created_desc',
        ?string $stockStatus = null,
        ?string $hasVariants = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        int $perPage = AppConstants::ADMIN_PAGINATION_LIMIT
    ): LengthAwarePaginator {
        $query = Product::with(['category', 'brand', 'variants']);

        // Filter by category
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter by brand
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        // Filter by stock status
        if ($stockStatus === 'in_stock') {
            $query->where('stock', '>', 0);
        } elseif ($stockStatus === 'low_stock') {
            $query->whereBetween('stock', [1, 10]);
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        // Filter by variant type
        if ($hasVariants === '1' || $hasVariants === 'true') {
            $query->where('has_variants', true);
        } elseif ($hasVariants === '0' || $hasVariants === 'false') {
            $query->where('has_variants', false);
        }

        // Filter by price range
        if ($minPrice !== null && $minPrice > 0) {
            $query->where(function ($q) use ($minPrice) {
                $q->where('price', '>=', $minPrice)
                  ->orWhere('sale_price', '>=', $minPrice);
            });
        }
        if ($maxPrice !== null && $maxPrice > 0) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('price', '<=', $maxPrice)
                  ->orWhere('sale_price', '<=', $maxPrice);
            });
        }

        // Filter by search query (Accent-Insensitive Vietnamese Search)
        if (!empty($search)) {
            $term = trim($search);
            $unaccentedTerm = \App\Helpers\VietnameseHelper::removeAccents($term);

            $query->where(function ($q) use ($term, $unaccentedTerm) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('search_index', 'LIKE', "%{$unaccentedTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$term}%")
                  ->orWhere('material', 'LIKE', "%{$term}%")
                  ->orWhere('color', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active products for storefront.
     *
     * @return Collection<int, Product>
     */
    public function getAllActiveProducts(): Collection
    {
        return Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new product with smart variants.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while (Product::where('slug', $data['slug'])->exists()) {
                $data['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }

            if (empty($data['sku'])) {
                $data['sku'] = 'BAG-' . strtoupper(Str::random(6));
            }

            $hasVariants = !empty($data['has_variants']) && !empty($data['variants']);
            $data['has_variants'] = $hasVariants;
            $data['is_featured'] = !empty($data['is_featured']);
            $data['is_active'] = !empty($data['is_active']);

            // Separate attributes and variants payload before product creation
            $attributesPayload = $data['attributes'] ?? [];
            $variantsPayload = $data['variants'] ?? [];
            unset($data['attributes'], $data['variants']);

            $product = Product::create($data);

            if ($hasVariants) {
                $this->syncAttributesAndVariants($product, $attributesPayload, $variantsPayload);
            }

            return $product->fresh(['category', 'brand', 'attributes.values', 'variants']);
        });
    }

    /**
     * Update an existing product and its smart variants.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if (isset($data['name']) && $data['name'] !== $product->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
                
                $originalSlug = $data['slug'];
                $count = 1;
                while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
                    $data['slug'] = "{$originalSlug}-{$count}";
                    $count++;
                }
            }

            $hasVariants = !empty($data['has_variants']) && !empty($data['variants']);
            $data['has_variants'] = $hasVariants;
            $data['is_featured'] = !empty($data['is_featured']);
            $data['is_active'] = !empty($data['is_active']);

            $attributesPayload = $data['attributes'] ?? [];
            $variantsPayload = $data['variants'] ?? [];
            unset($data['attributes'], $data['variants']);

            $product->update($data);

            if ($hasVariants) {
                $this->syncAttributesAndVariants($product, $attributesPayload, $variantsPayload);
            } else {
                // If switched off variants, remove old variant records
                $product->variants()->delete();
                $product->attributes()->delete();
            }

            return $product->fresh(['category', 'brand', 'attributes.values', 'variants']);
        });
    }

    /**
     * Synchronize Product Attributes and Matrix Variants.
     *
     * @param Product $product
     * @param array $attributesPayload
     * @param array $variantsPayload
     */
    protected function syncAttributesAndVariants(Product $product, array $attributesPayload, array $variantsPayload): void
    {
        // 1. Delete old attributes & variants
        $product->variants()->delete();
        $product->attributes()->delete();

        $valueMap = []; // GroupName => [ValueString => ValueId]

        // 2. Insert Attributes and Values
        $pos = 1;
        foreach ($attributesPayload as $attrGroup) {
            $groupName = trim($attrGroup['name'] ?? '');
            if (empty($groupName)) continue;

            $attribute = ProductAttribute::create([
                'product_id' => $product->id,
                'name' => $groupName,
                'position' => $pos++,
            ]);

            $rawValues = $attrGroup['values'] ?? [];
            if (is_string($rawValues)) {
                $rawValues = array_map('trim', explode(',', $rawValues));
            }

            foreach ($rawValues as $val) {
                $val = trim($val);
                if (empty($val)) continue;

                $valModel = ProductAttributeValue::create([
                    'product_attribute_id' => $attribute->id,
                    'value' => $val,
                ]);

                $valueMap[$groupName][$val] = $valModel->id;
            }
        }

        // 3. Insert Variants from Matrix
        $totalStock = 0;
        $minPrice = null;
        $minSalePrice = null;

        foreach ($variantsPayload as $index => $varRow) {
            $price = (float) ($varRow['price'] ?? $product->price);
            $salePrice = isset($varRow['sale_price']) && $varRow['sale_price'] !== '' && $varRow['sale_price'] !== null
                ? (float) $varRow['sale_price']
                : null;
            $stock = (int) ($varRow['stock'] ?? 0);
            $totalStock += $stock;

            if ($minPrice === null || $price < $minPrice) {
                $minPrice = $price;
                $minSalePrice = $salePrice;
            }

            $sku = !empty($varRow['sku']) ? trim($varRow['sku']) : ($product->sku . '-V' . ($index + 1));

            // Ensure unique SKU
            $originalSku = $sku;
            $skuCount = 1;
            while (ProductVariant::where('sku', $sku)->exists()) {
                $sku = "{$originalSku}-{$skuCount}";
                $skuCount++;
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $sku,
                'variant_title' => $varRow['variant_title'] ?? "Biến thể " . ($index + 1),
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => $stock,
                'image' => !empty($varRow['image']) ? $varRow['image'] : $product->image,
                'option1_value' => $varRow['option1_value'] ?? null,
                'option2_value' => $varRow['option2_value'] ?? null,
                'option3_value' => $varRow['option3_value'] ?? null,
                'is_active' => isset($varRow['is_active']) ? (bool) $varRow['is_active'] : true,
            ]);

            // Link pivot values
            $pivotIds = [];
            foreach (['option1_value', 'option2_value', 'option3_value'] as $optKey) {
                if (!empty($varRow[$optKey])) {
                    $valName = $varRow[$optKey];
                    foreach ($valueMap as $grp => $valDict) {
                        if (isset($valDict[$valName])) {
                            $pivotIds[] = $valDict[$valName];
                        }
                    }
                }
            }

            if (!empty($pivotIds)) {
                $variant->attributeValues()->sync(array_unique($pivotIds));
            }
        }

        // 4. Update Product base price & stock from variants
        if ($minPrice !== null) {
            $product->update([
                'price' => $minPrice,
                'sale_price' => $minSalePrice,
                'stock' => $totalStock,
            ]);
        }
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return (bool) $product->delete();
        });
    }

    /**
     * Toggle product active status.
     *
     * @param Product $product
     * @return bool
     */
    public function toggleStatus(Product $product): bool
    {
        $product->is_active = !$product->is_active;
        return $product->save();
    }
}
