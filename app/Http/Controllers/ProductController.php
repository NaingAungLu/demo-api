<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

use App\Representations\ProductRepresentation;
use App\Representations\ProductRepresentationCollection;

use DB;
use Log;
use Config;
use Storage;
use Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
        * @OA\Get(
        *    description     = "Get Product List",
        *    summary         = "Get Product List",
        *    operationId     = "getProductList",
        *    tags            = {"Product"},
        *    path            = "/product",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\Parameter(
        *        name        = "page",
        *        description = "Page",
        *        in          = "query",
        *        required    = false,
        *        @OA\Schema(
        *            type    = "string"
        *        ),
        *    ),
        *    @OA\Parameter(
        *        name        = "per_page",
        *        description = "Per Page",
        *        in          = "query",
        *        required    = false,
        *        @OA\Schema(
        *            type    = "integer"
        *        ),
        *    ),
        *    @OA\Parameter(
        *        name        = "keyword",
        *        description = "Keyword",
        *        in          = "query",
        *        required    = false,
        *        @OA\Schema(
        *            type    = "string"
        *        ),
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function index(Request $request)
    {
        $loggedinUser = $request->user();

        $page = $request->query('page');
        $per_page = $request->query('per_page');
        $keyword = $request->query('keyword');

		$name = $request->query('name');
		$remark = $request->query('remark');
		$is_discount = $request->query('is_discount');
		$quantity = $request->query('quantity');
		$price = $request->query('price');
		$date = $request->query('date');
        
        if(!$page) $page = 1;
        if(!$per_page) $per_page = Config::get('constants.LIMIT_PER_PAGE');

        $products = Product::select();

        if($keyword) {

        }


		if($name) {
			$products = $products->where('name', 'like', "%{$name}%");
		}

		if($remark) {
			$products = $products->where('remark', 'like', "%{$remark}%");
		}

		if($is_discount) {
			$products = $products->where('is_discount', $is_discount);
		}

		if($quantity) {
			$products = $products->where('quantity', $quantity);
		}

		if($price) {
			$products = $products->where('price', $price);
		}

		if($date) {
			$products = $products->whereRaw('DATE(date) = STR_TO_DATE(?, "%d/%m/%Y")', [ $date ]);
		}

        $products = $products->orderBy('updated_at', 'DESC');

        if($page == 'all') {
            $products = $products->get();

        } else {
            $products = $products->paginate($per_page, ['*'], 'page', $page);
        }

        return new ProductRepresentationCollection($products);
    }

    /**
        * @OA\Post(
        *    description = "Create Product",
        *    summary     = "Create Product",
        *    operationId = "createProduct",
        *    tags        = {"Product"},
        *    path        = "/product",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\RequestBody(
        *        @OA\MediaType(
        *            mediaType = "application/json",
        *            @OA\Schema(
		*                @OA\Property(property = "name", type = "string"),
		*                @OA\Property(property = "remark", type = "string"),
		*                @OA\Property(property = "is_discount", type = "integer"),
		*                @OA\Property(property = "quantity", type = "integer"),
		*                @OA\Property(property = "price", type = "integer"),
		*                @OA\Property(property = "date", type = "string"),
        *            )
        *        )
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function store(Request $request)
    {
        $loggedinUser = $request->user();

        $data = $request->json()->all();

        $validator = Validator::make($data, [
			'name' => 'required|string',
			'remark' => 'required|string',
			'is_discount' => 'required|integer',
			'quantity' => 'required|integer',
			'price' => 'nullable|numeric',
			'date' => 'nullable|date_format:d/m/Y', 
        ], trans('validation'), trans('validation.attributes'));
        
        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

        $newProduct = [
			'name' => $data['name'],
			'remark' => $data['remark'],
			'is_discount' => $data['is_discount'],
			'quantity' => $data['quantity'],

			'status' => Config::get('constants.STATUS.ACTIVE'),
			'created_by' => $loggedinUser['id'],
			'last_updated_by' => $loggedinUser['id'],
        ];

		if(array_key_exists('price', $data) && $data['price']) {
			$newProduct['price'] = $data['price'];
		}

		if(array_key_exists('date', $data) && $data['date']) {
			$newProduct['date'] = Carbon::createFromFormat('d/m/Y', $data['date']);
		}

        $product = Product::create($newProduct);

        if(!$product) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new ProductRepresentation($product);
    }

    /**
        * @OA\Get(
        *    description     = "Get Product By Id",
        *    summary         = "Get Product By Id",
        *    operationId     = "getProductById",
        *    tags            = {"Product"},
        *    path            = "/product/{id}",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\Parameter(
        *        name        = "id",
        *        description = "ID",
        *        in          = "path",
        *        required    = true,
        *        @OA\Schema(
        *            type    = "integer"
        *        ),
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function show(Request $request, $id)
    {
        $loggedinUser = $request->user();

        $product = Product::where('id', $id);
      
        if($product->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => 'Product'])], 400);
        }

        $product = $product->first();

        return new ProductRepresentation($product);
    }

    /**
        * @OA\Post(
        *    description     = "Update Product",
        *    summary         = "Update Product",
        *    operationId     = "updateProduct",
        *    tags            = {"Product"},
        *    path            = "/product/{id}",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\Parameter(
        *        name        = "id",
        *        description = "ID",
        *        in          = "path",
        *        required    = true,
        *        @OA\Schema(
        *            type    = "integer"
        *        ),
        *    ),
        *    @OA\RequestBody(
        *        @OA\MediaType(
        *            mediaType = "application/json",
        *            @OA\Schema(
		*                @OA\Property(property = "name", type = "string"),
		*                @OA\Property(property = "remark", type = "string"),
		*                @OA\Property(property = "is_discount", type = "integer"),
		*                @OA\Property(property = "quantity", type = "integer"),
		*                @OA\Property(property = "price", type = "integer"),
		*                @OA\Property(property = "date", type = "string"),
        *            )
        *        )
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function update(Request $request, $id)
    {
        $loggedinUser = $request->user();

        $product = Product::where('id', $id);
  
        if($product->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => 'Product'])], 400);
        }

        $product = $product->first();

        $data = $request->json()->all();

        $validator = Validator::make($data, [
			'name' => 'nullable|string',
			'remark' => 'nullable|string',
			'is_discount' => 'nullable|integer',
			'quantity' => 'nullable|integer',
			'price' => 'nullable|numeric',
			'date' => 'nullable|date_format:d/m/Y',    
        ], trans('validation'), trans('validation.attributes'));
        
        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

		$updateData = [
			'last_updated_by' => $loggedinUser['id']
		];

		if(array_key_exists('name', $data) && $data['name']) {
			$updateData['name'] = $data['name'];
		}

		if(array_key_exists('remark', $data) && $data['remark']) {
			$updateData['remark'] = $data['remark'];
		}

		if(array_key_exists('is_discount', $data) && $data['is_discount']) {
			$updateData['is_discount'] = $data['is_discount'];
		}

		if(array_key_exists('quantity', $data) && $data['quantity']) {
			$updateData['quantity'] = $data['quantity'];
		}

		if(array_key_exists('price', $data) && $data['price']) {
			$updateData['price'] = $data['price'];
		}

		if(array_key_exists('date', $data) && $data['date']) {
			$updateData['date'] = Carbon::createFromFormat('d/m/Y', $data['date']);
		}

		if(array_key_exists('status', $data) && $data['status']) {
			$updateData['status'] = $data['status'];
		}

        if(!$product->update($updateData)) {
            return response()->json([trans('messages.internal_error')], 500);
        }
        
        return new ProductRepresentation($product);
    }

    /**
        * @OA\Delete(
        *    description     = "Delete Product",
        *    summary         = "Delete Product",
        *    operationId     = "deleteProduct",
        *    tags            = {"Product"},
        *    path            = "/product/{id}",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\Parameter(
        *        name        = "id",
        *        description = "ID",
        *        in          = "path",
        *        required    = true,
        *        @OA\Schema(
        *            type    = "integer"
        *        ),
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function destroy(Request $request, $id)
    {
        $loggedinUser = $request->user();

        $product = Product::where('id', $id);
    
        if($product->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => 'Product'])], 400);
        }

        $product = $product->first();

        if($product['status'] == Config::get('constants.STATUS.INACTIVE')) {
            return response()->json([trans('messages.already_destory', ['name' => 'Product'])], 400);
        }

        if(!$product->update([
            'status' => Config::get('constants.STATUS.INACTIVE'),
            'last_updated_by' => $loggedinUser['id']
        ])) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new ProductRepresentation($product);
    }

    /**
        * @OA\Post(
        *    description     = "Restore Product",
        *    summary         = "Restore Product",
        *    operationId     = "restoreProduct",
        *    tags            = {"Product"},
        *    path            = "/product/{id}/restore",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\Parameter(
        *        name        = "id",
        *        description = "ID",
        *        in          = "path",
        *        required    = true,
        *        @OA\Schema(
        *            type    = "integer"
        *        ),
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function restore(Request $request, $id)
    {
        $loggedinUser = $request->user();

        $product = Product::where('id', $id);

        if($product->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => 'Product'])], 400);
        }

        $product = $product->first();

        if($product['status'] == Config::get('constants.STATUS.ACTIVE')) {
            return response()->json([trans('messages.already_restore', ['name' => 'Product'])], 400);
        }

        if(!$product->update([
            'status' => Config::get('constants.STATUS.ACTIVE'),
            'last_updated_by' => $loggedinUser['id']
        ])) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new ProductRepresentation($product);
    }

    /**
        * @OA\Delete(
        *    description     = "Permanent Delete Product",
        *    summary         = "Permanent Delete Product",
        *    operationId     = "permanentDeleteProduct",
        *    tags            = {"Product"},
        *    path            = "/product/{id}/permanent-delete",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\Parameter(
        *        name        = "id",
        *        description = "ID",
        *        in          = "path",
        *        required    = true,
        *        @OA\Schema(
        *            type    = "integer"
        *        ),
        *    ),
        *    @OA\Response(
        *        response    = 200,
        *        description = "Successful"
        *    )
        * )
    */
    public function permanentDelete(Request $request, $id)
    {
        $loggedinUser = $request->user();

        $product = Product::where('id', $id);
    
        if($product->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => 'Product'])], 400);
        }

        if(!$product->delete()) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return response()->json(["Success"], 200);
    }
}