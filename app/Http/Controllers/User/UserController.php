<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Serializers\CustomSerializer;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        abort_if(Gate::denies('admin'), Response::HTTP_FORBIDDEN, '403 การเข้าถึงถูกปฏิเสธ');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $users = User::query()->orderBy('id', 'desc')->paginate();
        return response()->json($this->transform($users));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($data['password']); // encrypt password before store to database
        $data['email_verified_at'] = now(); // verified
        $data['remember_token'] = Str::random(10);

        $user = User::query()->create($data);

        return response()->json($this->transform($user), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $user = User::query()->findOrFail($id);
        return response()->json($this->transform($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);

        $rules = [
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $user->fill($request->all());

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        abort_if(!$user->isDirty(), 422, 'คุณจำเป็นต้องระบุค่าที่แตกต่างเพื่อการปรับปรุงข้อมูล');

        $user->save();

        return response()->json($this->transform($user));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $user = User::query()->findOrFail($id);
        $user->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function transform($data)
    {
        return fractal($data, new UserTransformer(), new CustomSerializer())->toArray();
    }
}
