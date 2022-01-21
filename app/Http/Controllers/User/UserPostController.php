<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index($userId)
    {
        $user = User::query()->findOrFail($userId);
        return response()->json([
            'data' => $user->posts
        ]);
    }
}
