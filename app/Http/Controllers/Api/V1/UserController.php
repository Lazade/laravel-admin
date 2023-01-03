<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController {
  
  protected function validator(array $data) {
    return Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed'
    ]);
  }

  protected function list() {
    $usersData = User::all();
    $response = [
      'data' => $userData
    ];
    return response()->json($response);
  }

  protected function user($id) {
    try {
      $userData = User::where('id', $id)->firstOrFail();
      $response = [
        'data' => $userData
      ];
      return response()->json($response);
    } catch (\Throwable $th) {
      $response = [
        'error' => true,
        'msg' => 'Not Found the User with id{'. $id .'}'
      ];
      return response()->json($response);
    }
  }

  /**
   * Create a new user instance after a valid registeration
   * 
   * @param Request $request 
   * @return JSON
   */
  protected function create(Request $request) {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6'
    ], [
      'required' => 'The :attribute field is required.',
      'unique' => 'The :attribute existed'
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors();
      $response = [
        'error' => true,
        'msg' => $errors
      ];
      return $response;
    }
    $validated = $validator->validated();
    try {
      $preData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password'])
      ];
      $newUser = User::create($preData);
      $response = [
        'data' => $newUser
      ];
      return response()->json($response);
    } catch (\Throwable $th) {
      $response = [
        'error' => true,
        'msg' => $th
      ];
      return response()->json($response);
    }
  }

  protected function update($id, Request $request) {
    try {
      $user = User::where('id', $id)->firstOrFail();
      $validator = Validator::make($request->all(), [
        'name' => 'string|max:255',
        'email' => 'string|email|max:255|unique:users',
        'password' => 'string|min:6'
      ], [
        'unique' => 'The :attribute existed',
        'min' => 'The :attribute must be more than 6'
      ]);
      if ($validator->fails()) {
        $errors = $validator->errors();
        $response = [
          'error' => true,
          'msg' => $errors
        ];
        return response()->json($response);
      }
      $validated = $validator->validated();
      try {
        $updatedUser = $user->update($validated);
        $response = [
          'data' => $response
        ];
        return response()->json($response);
      } catch (\Throwable $th) {
        $response = [
          'error' => true,
          'msg' => $th
        ];
        return response()->json($response);
      }
    } catch (\Throwable $th) {
      $response = [
        'error' => true,
        'msg' => 'Could not find User with id{'.$id.'}'
      ];
      return response()->json($response);
    }
  }

  protected function delete($id) {
    try {
      $deletingUser = User::where('id', $id)->firstOrFail();
      $deleted = $deletingUser->delete();
      $response = [
        'data' => $deleted
      ];
      return response()->json($response);
    } catch (\Throwable $th) {
      $response = [
        'error' => true,
        'msg' => 'Could not find User with id{'.$id.'}'
      ];
      return response()->json($response);
    }
  }
}