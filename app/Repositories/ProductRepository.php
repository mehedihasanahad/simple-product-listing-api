<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepository implements ProductRepositoryInterface {
    /**
     * Retrieve all products.
     *
     * @return Collection
     */
    public function all(): Collection {
        return Product::all();
    }

    /**
     * Find a product by its ID or throw an exception if not found.
     *
     * @param int $id
     * @return Product
     *
     * @throws ModelNotFoundException
     */
    public function find($id): Product {
        return Product::findOrFail($id);
    }

    /**
     * Create a new product with the provided data.
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product {
        return Product::create($data);
    }

    /**
     * Update an existing product by its ID.
     *
     * @param int $id
     * @param array $data
     * @return Product
     *
     * @throws ModelNotFoundException
     */
    public function update($id, array $data): Product {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }
    
    /**
     * Delete a product by its ID.
     *
     * @param int $id
     * @return void
     *
     * @throws ModelNotFoundException
     */
    public function delete($id): void {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}
