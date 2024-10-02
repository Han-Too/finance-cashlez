<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return view('modules.users.index');
    }

    public function add()
    {
        $role = Role::with('permission')->select('id', 'name')->get();
        return view('modules.users.add', compact('role'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = User::where('username', $request->username)->first();

            if ($data) {
                return response()->json(['message' => "User Already Exist!", 'status' => false], 200);
            }

            if (empty($request->password) || empty($request->password_confirmation)) {
                return response()->json(['message' => "Input Password", 'status' => false], 200);
            }

            if ($request->password !== $request->password_confirmation) {
                return response()->json(['message' => "Password And Confirm Doesn't Match!", 'status' => false], 200);
            }

            $password = Hash::make($request->password);


            $create = User::create([
                'uuid' => Str::uuid(),
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password,
                'status' => "active",
            ]);

            if ($create) {
                $create->assignRole([$request->role_id]);
                DB::commit();
                return response()->json(['message' => "Successfully update data!", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            DB::commit();
            return response()->json(['message' => "Failed update data!", 'status' => false], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $data = User::where('uuid', $uuid)->first();
        $role = Role::with('permission')->select('id', 'name')->get();
        $mod = DB::table('model_has_roles')->where('model_id', $data->id)->first();
        return view('modules.users.edit', compact('data', 'role', 'mod'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = User::where('uuid', $request->uuid)->first();
            $data->username = $request->username;
            $data->name = $request->name;
            $data->email = $request->email;

            if ($request->password != '') {
                if ($request->password == $request->password_confirmation) {
                    $data->password = Hash::make($request->password);
                } else {
                    return response()->json(['message' => "Failed update data!", 'status' => false], 200);
                }
            }


            if ($data->save()) {
                $data->syncRoles([$request->role_id]);
                DB::commit();
                return response()->json(['message' => "Successfully update data!", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            DB::commit();
            return response()->json(['message' => "Failed update data!", 'status' => false], 200);
        }
    }

    public function destroy($uuid)
    {
        $data = User::where('uuid', $uuid)->first();
        $data->status = 'deleted';
        if ($data->save()) {
            $response = [
                'success' => true,
                'message' => "Berhasil Hapus Data",
            ];

            return response()->json($response, 200);
        }
    }


    public function userData(Request $request)
    {
        $query = User::with('roles')->where('status', '!=', 'deleted')->orderByDesc('created_at');

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }

    public function aktivasi()
    {
        return view('modules.users.aktivasi');
    }

    public function aktivasiData(Request $request)
    {
        $query = User::with('roles')->where('status',  'deleted')->orderByDesc('created_at');

        return DataTables::of($query->get())->addIndexColumn()->make(true);
    }

    public function activated($uuid)
    {
        $data = User::where('uuid', $uuid)->first();
        $data->status = 'active';
        if ($data->save()) {
            $response = [
                'success' => true,
                'message' => "Berhasil Mengaktivasi User",
            ];

            return response()->json($response, 200);
        }
    }
}
