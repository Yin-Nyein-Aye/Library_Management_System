<?php

namespace App\Http\Controllers;

use App\Contracts\UserInterface;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $user, $admin;
    public function __construct(
        private UserInterface $userInterface,
    ) {
        $this->middleware('auth:sanctum')->except('store');
        $this->user = Config::get('variables.ZERO');
        $this->admin = Config::get('variables.ONE');
    }

    public function index()
    {
        //
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
        $validatedData = $request->validate([
            'role' => 'required',
            'first_name'=>'required|string|max:225',
            'middle_name'=>'required|string|max:225',
            'last_name'=>'required|string|max:225',
            'email'=>'required|string|email|unique:users',
            'phone_no'=>'required|numeric|digits_between:9,11',
            'gender'=>'required|string',
            'password'=>'required|numeric|digits_between:4,6',
            'dob' => 'required'
        ]);
        if ($request->role === Config::get('variables.USER')) {
            $validatedData['role'] = $this->user;
        }
        elseif ($request->role === Config::get('variables.ADMIN')) {
            $validatedData['role'] = $this->admin;
        }
        $validatedData['password'] = Hash::make($request->password);

        $find_user = User::where('email', $request->email)->first();
        if ($find_user) {
            return response()->json('Email is already exist');
        }
        $user = $this->userInterface->store('User', $validatedData);
        $return_data = new UserResource($user);
        return response()->json([
            'data' => $return_data,
            'message' => 'Successfully Registered',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
