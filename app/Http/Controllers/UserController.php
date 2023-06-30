<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    try {
      $users = User::latest()
        ->when(request()->q != '', function ($q) {
          $q->where('name', 'LIKE', '%' . request()->q . '%');
        })
        ->paginate(25);

      return $this->generateResponse($users, '', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed get data users', 500);
    }
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // set validation rule
    $payload = collect([
      'name'       => 'required',
      'email'      => 'required|email|unique:users,email',
      'password'   => 'required',
    ]);

    // validate payload
    $validation = $request->validate($payload->all());

    try {
      User::create($validation);
      return $this->generateResponse([], 'user successfully created!', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed store user', 500);
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(User $user)
  {
    return $this->generateResponse($user, '', 200);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(User $user)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, User $user)
  {
    $payload = collect([
      'name'       => 'required',
      'email'      => 'required|email',
    ]);
    // validate payload
    $validation = $request->validate($payload->all());

    try {
      $user->update($validation);
      return $this->generateResponse([], 'user successfully updated!', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed updated user', 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $user)
  {
    try {
      $user->delete();
      return $this->generateResponse([], 'user successfully deleted!', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed destroy user', 500);
    }
  }
}
