<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
    * @OA\Get(
    * path="/api/v1/products",
    * operationId="getProducts",
    * tags={"Products"},
    * summary="Get product information.",
    * description="Return data.",
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="current_page", type="integer"),
    *         @OA\Property(
    *             property="data",
    *             type="array",
    *             @OA\Items(
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="sku", type="integer"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="quantity", type="integer"),
    *                 @OA\Property(property="price", type="number"),
    *                 @OA\Property(property="description", type="string"),
    *                 @OA\Property(property="image", type="string"),
    *                 @OA\Property(property="created_at", type="string", format="date-time"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time"),
    *             )
    *         ),
    *         @OA\Property(property="first_page_url", type="string"),
    *         @OA\Property(property="from", type="integer"),
    *         @OA\Property(property="last_page", type="integer"),
    *         @OA\Property(property="last_page_url", type="string"),
    *         @OA\Property(
    *             property="links",
    *             type="array",
    *              @OA\Items(
    *                  @OA\Property(property="url", type="string"),
    *                  @OA\Property(property="label", type="string"),
    *                  @OA\Property(property="active", type="boolean")
    *              )
    *         ),
    *         @OA\Property(property="next_page_url", type="integer"),
    *         @OA\Property(property="path", type="string"),
    *         @OA\Property(property="per_page", type="integer"),
    *         @OA\Property(property="prev_page_url", type="integer"),
    *         @OA\Property(property="to", type="integer"),
    *         @OA\Property(property="total", type="integer"),
    *     )
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return response()->json($products, 200);
    }

    /**
    * @OA\Post(
    * path="/api/v1/products",
    * operationId="postProducts",
    * tags={"Products"},
    * summary="Post product information.",
    * description="Return data.",
    * @OA\RequestBody(
    *     description="Data",
    *     @OA\MediaType(
    *         mediaType="multipart/form-data",
    *         @OA\Schema(
    *             @OA\Property(property="sku", type="integer"),
    *             @OA\Property(property="name", type="string"),
    *             @OA\Property(property="quantity", type="integer"),
    *             @OA\Property(property="price", type="number"),
    *             @OA\Property(property="description", type="string"),
    *             @OA\Property(property="image", type="string", format="file"),
    *         )
    *     )
    * ),
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="id", type="integer"),
    *         @OA\Property(property="sku", type="integer"),
    *         @OA\Property(property="name", type="string"),
    *         @OA\Property(property="quantity", type="integer"),
    *         @OA\Property(property="price", type="number"),
    *         @OA\Property(property="description", type="string"),
    *         @OA\Property(property="image", type="string"),
    *         @OA\Property(property="created_at", type="string", format="date-time"),
    *         @OA\Property(property="updated_at", type="string", format="date-time"),
    *     )
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku'           => 'required',
            'name'          => 'required|string',
            'quantity'      => 'required|integer',
            'price'         => 'required|numeric',
            'description'   => 'required',
            'image'         => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($request->hasFile('image')) {
            $filename = $request->file('image')->store('public/images');
            //return response()->json(['upload_file_not_found'], 400);
        }else{
            $filename = null;
        }

        $allowedfileExtension   = ['pdf','jpg','png'];
        $files                  = $request->file('image');
        $errors                 = [];

        $extension  = $request->file('image')->getClientOriginalExtension();
        $check      = in_array($extension, $allowedfileExtension);

        if(!$check){
             return response()->json([], 400);
        }

        $product                = new Product();
        $product->sku           = $request->get('sku');
        $product->name          = $request->get('name');
        $product->price         = $request->get('price');
        $product->quantity      = $request->get('quantity');
        $product->description   = $request->get('description');
        $product->image         = $filename;
        $product->save();

        return response()->json($product, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
    * @OA\Put(
    * path="/api/v1/products/{id}",
    * operationId="putProducts",
    * tags={"Products"},
    * summary="Put product information.",
    * description="Return data.",
    * @OA\Parameter(
    *     name="id",
    *     in="path",
    *     description="ID product",
    *     required=true,
    *     @OA\Schema(
    *         type="integer"
    *     )
    * ),
    * @OA\RequestBody(
    *     description="Data",
    *     @OA\MediaType(
    *         mediaType="multipart/form-data",
    *         @OA\Schema(
    *             @OA\Property(property="sku", type="integer"),
    *             @OA\Property(property="name", type="string"),
    *             @OA\Property(property="quantity", type="integer"),
    *             @OA\Property(property="price", type="number"),
    *             @OA\Property(property="description", type="string"),
    *             @OA\Property(property="image", type="string", format="file"),
    *         )
    *     )
    * ),
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="id", type="integer"),
    *         @OA\Property(property="sku", type="integer"),
    *         @OA\Property(property="name", type="string"),
    *         @OA\Property(property="quantity", type="integer"),
    *         @OA\Property(property="price", type="number"),
    *         @OA\Property(property="description", type="string"),
    *         @OA\Property(property="image", type="string"),
    *         @OA\Property(property="created_at", type="string", format="date-time"),
    *         @OA\Property(property="updated_at", type="string", format="date-time"),
    *     )
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json(['message'=>'Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'sku'           => 'required',
            'name'          => 'required|string',
            'quantity'      => 'required|integer',
            'price'         => 'required|numeric',
            'description'   => 'required',
            'image'         => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $path = public_path('storage/'.$product->image);
        if(File::exists($path)){
            File::delete($path);
        }

        if($request->hasFile('image')) {
            $filename = $request->file('image')->store('public/images');
            //return response()->json(['upload_file_not_found'], 400);
        }else{
            $filename = null;
        }

        $allowedfileExtension   = ['pdf','jpg','png'];
        $files                  = $request->file('image');
        $errors                 = [];

        $extension  = $request->file('image')->getClientOriginalExtension();
        $check      = in_array($extension, $allowedfileExtension);

        if(!$check){
             return response()->json([], 400);
        }

        //$product                = new Product();
        $product->sku           = $request->get('sku');
        $product->name          = $request->get('name');
        $product->price         = $request->get('price');
        $product->quantity      = $request->get('quantity');
        $product->description   = $request->get('description');
        $product->image         = $filename;
        $product->save();

        return response()->json($product, 200);
    }

    /**
    * @OA\Delete(
    * path="/api/v1/products/{id}",
    * operationId="deleteProduct",
    * tags={"Products"},
    * summary="Delete product information.",
    * description="Return data.",
    * @OA\Parameter(
    *     name="id",
    *     in="path",
    *     description="ID product",
    *     required=true,
    *     @OA\Schema(
    *         type="integer"
    *     )
    * ),
    * @OA\Response(
    *     response=204,
    *     description="No content",
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json([], 204);
        }
        return response()->json(['message'=>'Not Found'], 404);
    }

    /**
    * @OA\Get(
    * path="/api/v1/products/search",
    * operationId="searchProducts",
    * tags={"Products"},
    * summary="Search product information.",
    * description="Return data.",
    * @OA\Parameter(
    *     name="name",
    *     in="query",
    *     description="Name product",
    *     required=true,
    *     @OA\Schema(
    *         type="string"
    *     )
    * ),
    * @OA\Parameter(
    *     name="sku",
    *     in="query",
    *     description="Sku product",
    *     required=true,
    *     @OA\Schema(
    *         type="integer"
    *     )
    * ),
    *
    * @OA\Response(
    *     response=200,
    *     description="Successful operation",
    *     @OA\JsonContent(
    *         type="array",
    *         @OA\Items(
    *             @OA\Property(property="id", type="integer"),
    *             @OA\Property(property="sku", type="integer"),
    *             @OA\Property(property="name", type="string"),
    *             @OA\Property(property="quantity", type="integer"),
    *             @OA\Property(property="price", type="number"),
    *             @OA\Property(property="description", type="string"),
    *             @OA\Property(property="image", type="string"),
    *             @OA\Property(property="created_at", type="string", format="date-time"),
    *             @OA\Property(property="updated_at", type="string", format="date-time"),
    *         )
    *     )
    *  ),
    * @OA\Response(
    *     response=400,
    *     description="Bad Request"
    * ),
    * @OA\Response(
    *     response=401,
    *     description="Unauthenticated",
    * ),
    * @OA\Response(
    *     response=403,
    *     description="Forbidden"
    * )
    * )
    */
    public function search(Request $request){
        $name = $request->get('name');
        $sku = $request->get('sku');
        $product = Product::query()
                ->where('name','LIKE','%'.$name.'%')
                ->where('sku','LIKE','%'.$sku.'%')
                ->get();

        return response()->json($product, 200);
    }
}
