<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\PackageRepositoryInterface;
use App\Representations\PackageRepresentation;
use App\Representations\PackageRepresentationCollection;

use App\Library\APIResponse;

class OrderController extends Controller
{
    protected $package;
    
    public function __construct(PackageRepositoryInterface $package)
    {
        $this->package = $package;
    }

    public function index()
    {
        return (new APIResponse(0, 'Success', [
            'total_item' => $this->package->total(),
            'total_page' => 1,
            'mem_tier' => "newbie",
            'total_expired_class' => 0,
            'pack_list' => new PackageRepresentationCollection($this->package->all())
        ]))->getJson();
    }

    public function store()
    {
        $loggedinUser = $request->user();

        $data = $request->json()->all();

        $validator = Validator::make($data, [
            'disp_order' => 'required|integer',
            'pack_id' => 'required|string',
            'pack_name' => 'required|string',
            'pack_description' => 'required|string',
            'total_credit' => 'required|integer',
            'tag_name' => 'required|string',
            'validity_month' => 'required|integer',
            'pack_price' => 'nullable|numeric',
            'newbie_first_attend' => 'required|integer',
            'newbie_addition_credit' => 'required|integer',
            'newbie_note' => 'required|string',
            'pack_alias' => 'required|string',
            'estimate_price' => 'nullable|numeric', 
        ], trans('validation'), trans('validation.attributes'));
        
        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

        $newPackage = [
            'disp_order' => $data['disp_order'],
            'pack_id' => $data['pack_id'],
            'pack_name' => $data['pack_name'],
            'pack_description' => $data['pack_description'],
            'total_credit' => $data['total_credit'],
            'tag_name' => $data['tag_name'],
            'validity_month' => $data['validity_month'],
            'newbie_first_attend' => $data['newbie_first_attend'],
            'newbie_addition_credit' => $data['newbie_addition_credit'],
            'newbie_note' => $data['newbie_note'],
            'pack_alias' => $data['pack_alias'],

            'status' => Config::get('constants.STATUS.ACTIVE'),
            'created_by' => $loggedinUser['id'],
            'last_updated_by' => $loggedinUser['id'],
        ];

        if(array_key_exists('pack_price', $data) && $data['pack_price']) {
            $newPackage['pack_price'] = $data['pack_price'];
        }

        if(array_key_exists('estimate_price', $data) && $data['estimate_price']) {
            $newPackage['estimate_price'] = $data['estimate_price'];
        }

        $package = Package::create($newPackage);

        if(!$package) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new PackageRepresentation($package);
    }


}
