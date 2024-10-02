<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\Permission;
use App\Models\Privilege;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use Spatie\Permission\Models\Role as ModelsRole;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('modules.roles.index');
    }

    public function detail($slug)
    {
        $role = Role::with('permission')->select('id', 'name')->first();
        $data = Permission::select('id', 'name')->get();

        return response()->json(['data' => $data, 'header' => $role, 'message' => 'Successfully get data!', 'status' => true], 200);
    }
    public function add()
    {
        $data = Role::with('permission')->get();
        $permission = Permission::get();
        return view('modules.roles.role.add', compact('data', 'permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Role::with('permission')->where('id', $id)->first();
        $permission = Permission::get();
        return view('modules.roles.role.edit', compact('data', 'permission'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            // Mengambil data role berdasarkan ID
            $tesrole = ModelsRole::where('name', $request->named)->first();

            if($tesrole){
                return response()->json(['message' => "Role Name Has Already!", 'status' => false], 200);
            }

            $role = ModelsRole::create([
                "name" => $request->named,
            ]);

            // Simpan data role
            if ($role) {

                // Mengambil semua permission_id dari request
                // $permissions = $request->permission('permissions', []);
                $permissions = $request->permissions;

                // Loop setiap permission_id
                foreach ($permissions as $permissionId) {
                    // Memastikan bahwa kombinasi role_id dan permission_id tidak ada sebelum menambahkan
                    $permission = Permission::where('id', $permissionId)->pluck('name');
                    // Log::info($permission);

                    // Berikan permission ke role menggunakan Spatie's givePermissionTo
                    $role->givePermissionTo($permission);
                }

                DB::commit();
                return response()->json(['message' => "Successfully add data!", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            DB::rollBack();
            return response()->json(['message' => "Failed to update data!", 'status' => false], 200);
        }

    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $permisname = [];
        DB::beginTransaction();
        try {
            // Mengambil data role berdasarkan ID
            $role = ModelsRole::where('name', $request->nameold)->first();
            Log::info($role);
            $role->name = $request->named;
            $role->updated_at = Carbon::now();

            // Simpan data role
            if ($role->save()) {

                // DB::table("role_has_permissions")->where("role_id", $role->id)->delete();

                // Mengambil semua permission_id dari request
                // $permissions = $request->permission('permissions', []);
                $permissions = $request->permissions;
                
                // Loop setiap permission_id
                foreach ($permissions as $permissionId) {
                    // Memastikan bahwa kombinasi role_id dan permission_id tidak ada sebelum menambahkan
                    $permission = Permission::where('id', $permissionId)->pluck('name');

                    $permisname[] = $permission;
                    // Log::info($permission);
                    // Berikan permission ke role menggunakan Spatie's givePermissionTo
                    // $role->givePermissionTo($permission);
                }
                $role->syncPermissions($permisname);

                DB::commit();
                return response()->json(['message' => "Successfully update data!", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            DB::rollBack();
            return response()->json(['message' => "Failed to update data!", 'status' => false], 200);
        }

    }

    public function destroy($id)
    {

        $role = ModelsRole::find($id);

        if ($role) {
            
            DB::table("model_has_roles")->where("role_id", $role->id)->delete();
            DB::table("role_has_permissions")->where("role_id", $role->id)->delete();

            $role->delete();

            return response()->json(['message' => "Successfully delete data!", 'status' => true], 200);
        }
    }

    public function data(Request $request)
    {
        $items = Role::with('permission')->get();

        // return DataTables::of($query->get())->addIndexColumn()->make(true);
        return DataTables::of($items)
            ->addColumn('roles_count', function ($item) {
                return Utils::countTotalRoles($item->id); // Menggunakan fungsi helper
            })
            ->make(true);
    }

    public function countTotalRoles($id)
    {
        $count = Utils::countTotalRoles($id);
        return response()->json(['count' => $count]);
    }

    public function privilege(Request $request)
    {
        $id = $request->id;
        $role = Role::where('id', $id)->first();

        DB::beginTransaction();
        try {
            $privileges = Privilege::where('role_id', $id)->where('status', 'active')->get();
            foreach ($privileges as $key => $value) {
                $valId = $value->id;
                $item = Privilege::where('id', $valId)->first();
                $varRead = 'read_' . $valId;
                $varCreate = 'create_' . $valId;
                $varUpdate = 'update_' . $valId;
                $varDelete = 'delete_' . $valId;

                if (isset($request->$varRead)) {
                    $item->read = true;
                } else {
                    $item->read = false;
                }
                if (isset($request->$varCreate)) {
                    $item->create = true;
                } else {
                    $item->create = false;
                }
                if (isset($request->$varUpdate)) {
                    $item->update = true;
                } else {
                    $item->update = false;
                }
                if (isset($request->$varDelete)) {
                    $item->delete = true;
                } else {
                    $item->delete = false;
                }
                $item->save();
            }
            $role->title = $request->role_name;
            $role->save();
            DB::commit();
            return response()->json(['message' => "Successfully update data!", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => "Failed update data!", 'status' => false], 200);
        }
    }
}
