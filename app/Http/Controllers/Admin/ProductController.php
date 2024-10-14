<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        try {
            $products = $this->productService->getPaginatedProduct();

            if (request()->ajax()) {
                $view = view('admin.product.table', compact('products'))->render(); // Thay $campaigns bằng $products
                return response()->json(['success' => true, 'table' => $view]);
            }
            return view('admin.product.index', compact('products'));
        } catch (Exception $e) {
            Log::error('Failed to get Paginated product list: ' . $e->getMessage());
            return ApiResponse::error('Failed to get paginated product list', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Tạo sản phẩm mới
            $this->productService->createNewProduct($request->all());

            // Lấy lại danh sách sản phẩm đã phân trang
            $products = $this->productService->getPaginatedProduct();

            // Render lại bảng sản phẩm và phân trang
            $table = view('admin.product.table', compact('products'))->render();
            $pagination = $products->links('vendor.pagination.custom')->render();

            return response()->json([
                'success' => true,
                'message' => 'Thêm sản phẩm thành công',
                'table' => $table,
                'pagination' => $pagination
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create new Product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Thêm sản phẩm thất bại',
            ]);
        }
    }


    public function update(Request $request)
    {
        try {
            $this->productService->updateProduct($request->all(), $request->input('id')); // Lấy ID từ input ẩn
            $products = $this->productService->getPaginatedProduct();
            $table = view('admin.product.table', compact('products'))->render();
            $pagination = $products->links('vendor.pagination.custom')->render();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công',
                'table' => $table,
                'pagination' => $pagination
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update Product: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình cập nhật sản phẩm.']);
        }
    }

    public function delete()
    {
        try {
            $this->productService->deleteProduct(request()->id);
            // Lấy lại danh sách sản phẩm đã phân trang
            $products = $this->productService->getPaginatedProduct();

            // Render lại bảng sản phẩm và phân trang
            $table = view('admin.product.table', compact('products'))->render();
            $pagination = $products->links('vendor.pagination.custom')->render();

            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công',
                'table' => $table,
                'pagination' => $pagination
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xóa sản phẩm thất bại'
            ]);
        }
    }




    public function fetch()
    {
        $products = Product::orderByDesc('created_at')->pagination(10);

        return view('admin.product.table', compact('products'))->render();
    }
}
