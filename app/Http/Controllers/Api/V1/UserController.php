<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoValidationRequest;
use App\Http\Requests\UpdateUserValidationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserValidationRequest $request, User $user)
    {
        $user->update($request->validated());

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);

    }

    public function uploadCustomerPhoto(PhotoValidationRequest $request, User $user)
    {

        try {
            $user->photo = $request->photo ?? null;
            $user->save();
            Log::info('Before Job dispatch User:' . $user);

            return response()->json([

                'message' => "image uploaded successfully",

            ], 200);
        } catch (\Exception$exception) {

            return response([

                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function getUser(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        return response()->json($user, 200);
    }

}