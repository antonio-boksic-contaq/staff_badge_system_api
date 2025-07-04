<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function __invoke()
    {

        return response()->json(new UserResource(Auth::user()));
    }
}