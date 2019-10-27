<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\OrderRepositoryInterface;

use App\Models\Promotion;
use App\Models\Package;
use App\Representations\OrderRepresentation;
use App\Representations\OrderRepresentationCollection;

use App\Library\APIResponse;

use DB;
use Log;
use Config;
use Storage;
use Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $order;
    
    public function __construct(OrderRepositoryInterface $order)
    {
        $this->order = $order;
    }

    public function index()
    {
        return (new APIResponse(0, 'Success', [
            'total_item' => $this->order->total(),
            'order_list' => new OrderRepresentationCollection($this->order->all())
        ]))->getJson();
    }

    public function show(Request $request, $id)
    {
        return (new APIResponse(0, 'Success', [
            'promotion_list' => new OrderRepresentation($this->order->show($id))
        ]))->getJson();
    }

    public function store(Request $request)
    {
        $loggedinUser = $request->user();

        $data = $request->json()->all();

        $validator = Validator::make($data, [
            'package_id' => 'required|integer',
            'promo_code' => 'nullable|string',
        ], trans('validation'), trans('validation.attributes'));
        
        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return (new APIResponse(400, 'Require Data'))->getJson();
        }

        $package = Package::where('id', $data['package_id']);
        if($package->doesntExist()) {
            return (new APIResponse(400, 'Invalid Package id'))->getJson();
        }
        $package = $package->first();

        $newOrder = [
            'package_id' => $data['package_id'],
            'order_date' => Carbon::now(),
            'grand_total' => $package['pack_price'],
            
            'status' => Config::get('constants.STATUS.ACTIVE'),
            'created_by' => $loggedinUser['id'],
            'last_updated_by' => $loggedinUser['id'],
        ];

        if(array_key_exists('promo_code', $data) && $data['promo_code']) {
            
            $promotion = Promotion::where('promo_code', $data['promo_code']);
            if($promotion->doesntExist()) {
                return (new APIResponse(400, 'Invalid Promo Code'))->getJson();
            } 
            $promotion = $promotion->first();
            $newOrder['grand_total'] = $newOrder['grand_total'] - $promotion['amount'];
        }

        if(!$this->order->save($newOrder)) {
            return (new APIResponse(0, 'Internal Server Error'))->getJson();
        }

        return (new APIResponse(0, 'Success'))->getJson();
    }


}
