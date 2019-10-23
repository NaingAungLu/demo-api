<?php

namespace @namespace\Http\Controllers;

use Illuminate\Http\Request;

use @namespace\Models\@model_name;

use @namespace\Representations\@model_nameRepresentation;
use @namespace\Representations\@model_nameRepresentationCollection;

use DB;
use Log;
use Config;
use Storage;
use Validator;
use Carbon\Carbon;

class @model_nameController extends Controller
{
    /**
        * @OA\Get(
        *    description     = "Get @title List",
        *    summary         = "Get @title List",
        *    operationId     = "get@model_nameList",
        *    tags            = {"@title"},
        *    path            = "/@route",
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
@view1
        
        if(!$page) $page = 1;
        if(!$per_page) $per_page = Config::get('constants.LIMIT_PER_PAGE');

        $@variable_name_plural = @model_name::select();
@view2
        if($keyword) {

        }
@view3
        $@variable_name_plural = $@variable_name_plural->orderBy('updated_at', 'DESC');

        if($page == 'all') {
            $@variable_name_plural = $@variable_name_plural->get();

        } else {
            $@variable_name_plural = $@variable_name_plural->paginate($per_page, ['*'], 'page', $page);
        }

        return new @model_nameRepresentationCollection($@variable_name_plural);
    }

    /**
        * @OA\Post(
        *    description = "Create @title",
        *    summary     = "Create @title",
        *    operationId = "create@model_name",
        *    tags        = {"@title"},
        *    path        = "/@route",
        *    security        = {{ "passport": {"*"} }},
        *    @OA\RequestBody(
        *        @OA\MediaType(
        *            mediaType = "application/json",
        *            @OA\Schema(
@view6
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
@view7 
        ], trans('validation'), trans('validation.attributes'));
        
        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

        $new@model_name = [
@view8
        ];
@view9
        $@variable_name = @model_name::create($new@model_name);

        if(!$@variable_name) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new @model_nameRepresentation($@variable_name);
    }

    /**
        * @OA\Get(
        *    description     = "Get @title By Id",
        *    summary         = "Get @title By Id",
        *    operationId     = "get@model_nameById",
        *    tags            = {"@title"},
        *    path            = "/@route/{id}",
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

        $@variable_name = @model_name::where('id', $id);
@view12      
        if($@variable_name->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => '@title'])], 400);
        }

        $@variable_name = $@variable_name->first();

        return new @model_nameRepresentation($@variable_name);
    }

    /**
        * @OA\Post(
        *    description     = "Update @title",
        *    summary         = "Update @title",
        *    operationId     = "update@model_name",
        *    tags            = {"@title"},
        *    path            = "/@route/{id}",
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
@view6
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

        $@variable_name = @model_name::where('id', $id);
@view12  
        if($@variable_name->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => '@title'])], 400);
        }

        $@variable_name = $@variable_name->first();

        $data = $request->json()->all();

        $validator = Validator::make($data, [
@view10    
        ], trans('validation'), trans('validation.attributes'));
        
        if($validator->fails()) {
            $error_messages = [];
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $error_messages[] = $message;
            }
            return response()->json($error_messages, 400);
        }

@view11
        if(!$@variable_name->update($updateData)) {
            return response()->json([trans('messages.internal_error')], 500);
        }
        
        return new @model_nameRepresentation($@variable_name);
    }

    /**
        * @OA\Delete(
        *    description     = "Delete @title",
        *    summary         = "Delete @title",
        *    operationId     = "delete@model_name",
        *    tags            = {"@title"},
        *    path            = "/@route/{id}",
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

        $@variable_name = @model_name::where('id', $id);
@view12    
        if($@variable_name->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => '@title'])], 400);
        }

        $@variable_name = $@variable_name->first();

        if($@variable_name['status'] == Config::get('constants.STATUS.INACTIVE')) {
            return response()->json([trans('messages.already_destory', ['name' => '@title'])], 400);
        }

        if(!$@variable_name->update([
            'status' => Config::get('constants.STATUS.INACTIVE'),
            'last_updated_by' => $loggedinUser['id']
        ])) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new @model_nameRepresentation($@variable_name);
    }

    /**
        * @OA\Post(
        *    description     = "Restore @title",
        *    summary         = "Restore @title",
        *    operationId     = "restore@model_name",
        *    tags            = {"@title"},
        *    path            = "/@route/{id}/restore",
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

        $@variable_name = @model_name::where('id', $id);
@view12
        if($@variable_name->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => '@title'])], 400);
        }

        $@variable_name = $@variable_name->first();

        if($@variable_name['status'] == Config::get('constants.STATUS.ACTIVE')) {
            return response()->json([trans('messages.already_restore', ['name' => '@title'])], 400);
        }

        if(!$@variable_name->update([
            'status' => Config::get('constants.STATUS.ACTIVE'),
            'last_updated_by' => $loggedinUser['id']
        ])) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return new @model_nameRepresentation($@variable_name);
    }

    /**
        * @OA\Delete(
        *    description     = "Permanent Delete @title",
        *    summary         = "Permanent Delete @title",
        *    operationId     = "permanentDelete@model_name",
        *    tags            = {"@title"},
        *    path            = "/@route/{id}/permanent-delete",
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

        $@variable_name = @model_name::where('id', $id);
@view12    
        if($@variable_name->doesntExist()) {
            return response()->json([trans('messages.invalid', ['name' => '@title'])], 400);
        }

        if(!$@variable_name->delete()) {
            return response()->json([trans('messages.internal_error')], 500);
        }

        return response()->json(["Success"], 200);
    }
}