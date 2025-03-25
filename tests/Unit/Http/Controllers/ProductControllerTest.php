<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database before each test

    protected $productRepository;

    public function setUp(): void
    {
        parent::setUp();
        
        // Mock the ProductRepositoryInterface
        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->app->instance(ProductRepositoryInterface::class, $this->productRepository);
    }

    /** @test */
    public function it_can_list_all_products()
    {
        $products = Product::factory()->count(3)->make(); // Create fake product data

        $this->productRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($products);

        $response = $this->getJson(route('products.index'));

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson($products->toArray());
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 99.99,
            'category' => 'Electronics'
        ];

        $this->productRepository
            ->shouldReceive('create')
            ->once()
            ->with($productData)
            ->andReturn((object) $productData);

        $response = $this->postJson(route('products.store'), $productData);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'message' => 'Product created successfully',
                     'data' => $productData
                 ]);
    }

    /** @test */
    public function it_can_show_a_product_by_id()
    {
        $product = Product::factory()->make(['id' => 1]);

        $this->productRepository
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($product);

        $response = $this->getJson(route('products.show', ['product' => 1]));

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson($product->toArray());
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $productData = [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 150.00,
            'category' => 'Updated Category'
        ];

        $this->productRepository
            ->shouldReceive('update')
            ->with(1, $productData)
            ->once()
            ->andReturn((object) $productData);

        $response = $this->putJson(route('products.update', ['product' => 1]), $productData);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'message' => 'Product updated successfully',
                     'data' => $productData
                 ]);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $this->productRepository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(['message' => 'Product deleted successfully']);

        $response = $this->deleteJson(route('products.destroy', ['product' => 1]));

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['message' => 'Product deleted successfully']);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}