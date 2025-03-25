<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for Managing Products"
 * )
 */
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
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Product 1"),
     *                 @OA\Property(property="description", type="string", example="A sample product"),
     *                 @OA\Property(property="price", type="number", format="float", example=100.50),
     *                 @OA\Property(property="category", type="string", example="Electronics"),
     *                 @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-25T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-25T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No products found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No products available."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse {
        // Retrieve all products from the repository
        $products = $this->productRepository->all();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'No products available.',
                'data' => []
            ], Response::HTTP_OK);
        }

        // Fetch all products using the product repository and return as JSON
        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","price","category"},
     *                 @OA\Property(property="name", type="string", example="Product 1"),
     *                 @OA\Property(property="description", type="string", example="A sample product"),
     *                 @OA\Property(property="price", type="number", format="float", example=100.50),
     *                 @OA\Property(property="category", type="string", example="Electronics"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product Created Successfully",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="message", type="string", example="Product created successfully"),
     *            @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="message", type="string", example="The {name} field is required."),
     *            @OA\Property(property="errors", type="object")
     *         )
     *     ),
     * 
     * )
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
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get a single product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Product 1"),
     *             @OA\Property(property="description", type="string", example="A sample product"),
     *             @OA\Property(property="price", type="number", format="float", example=100.50),
     *             @OA\Property(property="category", type="string", example="Electronics"),
     *             @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-25T12:34:56Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-25T12:34:56Z"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Product] {id}")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse {
        // Retrieve a product by its ID from the repository
        $product = $this->productRepository->find($id);

        // Return the product data as JSON
        return response()->json($product);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/products/{id}",
     *     summary="Update existing product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"_method", "name", "price", "category"},
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *                 @OA\Property(property="name", type="string", example="Product 1"),
     *                 @OA\Property(property="description", type="string", example="A sample product"),
     *                 @OA\Property(property="price", type="number", format="float", example=100.50),
     *                 @OA\Property(property="category", type="string", example="Electronics"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product Created Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="message", type="string", example="The {name} field is required."),
     *            @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Product] {id}")
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product Deleted Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Product] {id}")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse {
        // Delete the product by its ID
        $this->productRepository->delete($id);

        // Return a success message as JSON
        return response()->json(['message' => 'Product deleted successfully'], Response::HTTP_OK);
    }
}
