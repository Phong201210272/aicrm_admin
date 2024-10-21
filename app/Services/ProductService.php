<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getPaginatedProduct()
    {
        try {
            return $this->product->where('user_id', Auth::user()->id)->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to get paginated product list: ' . $e->getMessage());
            throw new Exception('Failed to get paginated product list');
        }
    }

    public function getAllProducts()
    {
        try {
            return $this->product->where('user_id', Auth::user()->id)->orderBey('created_at')->get();
        } catch (Exception $e) {
            Log::error('Failed to get product list:' . $e->getMessage());
            throw new Exception('Failed to get product list');
        }
    }

    public function createNewProduct(array $data)
    {
        DB::beginTransaction();
        try {
            $product = $this->product->create([
                'name' => $data['name'],
                'user_id' => Auth::user()->id,
            ]);
            DB::commit();
            return $product;
        } catch (Exception $e) {
            Log::error('Failed to create new product: ' . $e->getMessage());
        }
    }

    public function updateProduct(array $data, $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($id);

            // Update product information
            $product->update([
                'name' => $data['name'],
            ]);

            DB::commit();
            return $product;  // Return the product object instead of the result of the update
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update this product: ' . $e->getMessage());
            throw new Exception('Failed to update this product');
        }
    }


    public function deleteProduct($id)
    {
        DB::beginTransaction();
        try {
            $product = $this->product->find($id);
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }
            $product->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete this product: ' . $e->getMessage());
            throw new Exception('Failed to delete this product');
        }
    }
}
