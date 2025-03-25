<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller {
    protected ProductRepositoryInterface $productRepository;

    /**
     * ProductController constructor.
     * 
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository) {
        // Injecting the product repository interface into the controller
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the products.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse {
        // Fetch all products using the product repository and return as JSON
        return response()->json($this->productRepository->all());
    }

    /**
     * Store a newly created product.
     * 
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse {
        // Handle image upload if provided
        $imageUrl = null;
        $requestData = $request->validated();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the image in the 'public/images/products' directory as filesystems.php configuration specifies
            $imageUrl = $request->file('image')->store('products');
        }
        if ($imageUrl) {
            $requestData = array_merge($request->validated(), ['image' => '/images/' . $imageUrl]);
        }
        // Create a new product using validated data from the request
        $product = $this->productRepository->create($requestData);

        // Return the newly created product with a 201 status code
        return response()->json(['message' => 'Product created successfully', 'data' => $product], Response::HTTP_CREATED);
    }

    /**
     * Display the specified product by ID.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse {
        // Retrieve a product by its ID from the repository
        $product = $this->productRepository->find($id);

        // Return the product data as JSON
        return response()->json($product);
    }

    /**
     * Update the specified product by ID.
     * 
     * @param ProductRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, $id): JsonResponse {
        // Handle image upload if provided
        $imageUrl = null;
        $requestData = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store the image in the 'public/images/products' directory as filesystems.php configuration specifies
            $imageUrl = $request->file('image')->store('products');
        }

        if ($imageUrl) {
            $requestData = array_merge($request->validated(), ['image' => '/images/' . $imageUrl]);
        }

        // Update the product using validated data from the request
        $updatedProduct = $this->productRepository->update($id, $requestData);

        // Return the updated product as JSON
        return response()->json(['message' => 'Product updated successfully', 'data' => $updatedProduct], Response::HTTP_OK);
    }

    /**
     * Remove the specified product from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse {
        // Delete the product by its ID
        $this->productRepository->delete($id);

        // Return a success message as JSON
        return response()->json(['message' => 'Product deleted successfully'], Response::HTTP_OK);
    }
}
