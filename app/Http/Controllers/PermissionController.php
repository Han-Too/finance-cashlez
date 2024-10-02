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


use Spatie\Permission\Models\Permission as permis;

class PermissionController extends Controller
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

    public function get($id)
    {
        $data = Permission::where('id', $id)->first();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            // Mengambil data role berdasarkan ID
            $permis = Permission::where('name', $request->named)->first();

            if ($permis) {
                return response()->json(['message' => "Permission Name Has Already!", 'status' => false], 200);
            }

            permis::create(["name" => $request->named]);

            DB::commit();
            return response()->json(['message' => "Successfully add data!", 'status' => true], 200);
        } catch (\Throwable $th) {
            Log::info($th);
            DB::rollBack();
            return response()->json(['message' => "Failed to update data!", 'status' => false], 200);
        }

    }
    public function update(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            // Mengambil data role berdasarkan ID
            $permis = Permission::where('id', $request->id)->first();

            // Simpan data role
            if ($permis) {

                $permis->name = $request->named;
                $permis->updated_at = Carbon::now();

                $permis->save();

                DB::commit();
                return response()->json(['message' => "Successfully update data!", 'status' => true], 200);
            } else {
                DB::rollBack();
                return response()->json(['message' => "Data Not Found!", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            DB::rollBack();
            return response()->json(['message' => "Failed to update data!", 'status' => false], 200);
        }

    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::find($id);

            if ($permission) {

                DB::table("model_has_permissions")->where("permission_id", $permission->id)->delete();
                DB::table("role_has_permissions")->where("permission_id", $permission->id)->delete();

                $permission->delete();

                DB::commit();
                return response()->json(['message' => "Successfully Delete Data!", 'status' => true], 200);
            } else {
                DB::rollBack();
                return response()->json(['message' => "Failed Delete Data!", 'status' => false], 200);
            }
        } catch (\Throwable $th) {
            Log::info($th);
            DB::rollBack();
            return response()->json(['message' => "Failed to update data!", 'status' => false], 200);
        }
    }

    public function data(Request $request)
    {
        $items = Permission::orderBy('created_at', 'desc')->get();

        return DataTables::of($items)->addIndexColumn()->make(true);
    }

    public function countTotalRoles($id)
    {
        $count = Utils::countTotalRoles($id);
        return response()->json(['count' => $count]);
    }

}
