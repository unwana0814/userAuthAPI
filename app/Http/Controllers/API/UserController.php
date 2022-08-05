<?php

namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends BaseController
{
    /**
     * Create User API
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'confirmpassword' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        (array) $data = $request->all();
        unset($data['confirmpassword']);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $success['token'] =  $user->createToken('UserAuthAPI')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login User API
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('UserAuthAPI')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }

        return $this->sendError('Unauthorised.', ['error' => 'Incorrect email or password.']);
    }

    /**
     * Get Single User API
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getuser($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }

    /**
     * Get All Users API
     *
     * @return \Illuminate\Http\Response
     */
    public function getusers()
    {
        $users = User::all();

        return $this->sendResponse(UserResource::collection($users), 'All User retrieved successfully.');
    }

    /**
     * Update User API
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string',
            'email' => 'unique:users,email',
            'password' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->has('password')) {
            $data['password'] =  bcrypt($data['password']);
        }
        $user = User::where('id', $id)->first();

        if ($user) {

            $user->update($data);

            return $this->sendResponse(new UserResource($user), 'User updated successfully.');
        }

        return $this->sendError('User not updated');
    }

    /**
     * Delete User API
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        $data = $request->all();
        $user = User::where('id', $id)->first();

        if ($user) {

            $user->delete($data);

            return $this->sendResponse([], 'User deleted successfully.');
        }

        return $this->sendError('User not deleted');
    }
}
