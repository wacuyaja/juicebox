<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //

    public function index()
    {
        $data = Posts::with('User')->paginate('5');

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => $validator->getMessageBag(),
                ]);
            }

            Posts::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'description' => $request->description
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'success submited successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function show($id)
    {

        try {
            $data = Posts::with('User')->find($id);

            $status = 200;
            $message = 'success';

            if (!$data) {
                $status = 404;
                $message = 'Data not found';
            }


            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => $validator->getMessageBag(),
                ]);
            }


            $data = Posts::find($id);
            if($data->user_id != Auth::user()->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'You have no access to this action',
                ]);
            }

            Posts::find($id)->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'success updated successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $data = Posts::find($id);

            if (!$data) {
                $status = 404;
                $message = 'Data not found';
            }

            if($data->user_id != Auth::user()->id) {
                $status = 403;
                $message = 'You have no access to this action';
            }

            $data->delete();

            $status = 200;
            $message = 'data deleted successfully';

            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
