<?php

namespace App\Http\Controllers\API;

use App\Role;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class RolesController extends Controller {
    public function list() {
        $user = Auth::user();
        $roles = Role::query();

        if ( $user->role == 'Administrator' ) {
            if ( $_GET['dropdown_list'] != 'true' ) {
                $result = $roles
                    ->orderBy( 'created_at', 'asc' )
                    ->get();

                return response([
                    'roles'     => $result
                ], 200 );
            } else {
                $result = $roles
                    ->orderBy( 'created_at', 'asc' )
                    ->pluck( 'name' );

                return response([
                    'roles'     => $result
                ], 200 );
            }
        } else {
            return response([
                'message'   => 'You are not allowed to view this.'
            ], 401 );
        }
    }

    public function add( Request $request ) {
        $user = Auth::user();

        if ( $user->role == 'Administrator' ) {
            if ( $request->all() ) {
                $rules = array(
                    'name'          => 'required|unique:roles,name,{$id},id,deleted_at,NULL',
                    'description'   => 'required'
                );

                $validator = Validator::make( Input::all(), $rules );

                if ( $validator->fails() ) {
                    return response( $validator->messages(), 422 );
                } else {
                    $insert_role = Role::create([
                        'name'          => $request->name,
                        'description'   => $request->description
                    ]);
        
                    if ( $insert_role ) {
                        return response([
                            'message'   => 'Role has been added.'
                        ], 200 );
                    } else {
                        return response([
                            'message'   => 'Sorry there\'s a problem on creating user role. Please try again.'
                        ], 500 );
                    }
                }
            }
        } else {
            return response([
                'message'   => 'You are not allowed here.'
            ], 401 );
        }
    }

    public function update( Request $request ) {
        $user = Auth::user();
        $role_id = $request->role_id;

        if ( $user->role == 'Administrator' ) {
            if ( $role_id != 1 ) {
                $role = Role::find( $role_id );
                $role->name = $request->update_name;
                $role->description = $request->update_description;
                $update_role = $role->save();
    
                if ( $update_role ) {
                    return response([
                        'message'   => 'User role has been updated.'
                    ], 200 );
                } else {
                    return response([
                        'message'   => 'Sorry there\'s an error updating user role. Please try again.'
                    ], 500 );
                }
            } else {
                return response([
                    'message'   => 'This user role is not editable.'
                ], 401 );
            }
        } else {
            return response([
                'message'   => 'You are not allowed here.'
            ], 401 );
        }
    }

    public function delete( Request $request ) {
        $user = Auth::user();
        $role_id = $request->role_id;

        if ( $user->role == 'Administrator' ) {
            if ( $role_id != 1 ) {
                $role = Role::find( $role_id );
                $delete_role = $role->delete();

                if ( $delete_role ) {
                    return response([
                        'message'   => 'User role has been deleted.'
                    ], 200 );
                } else {
                    return response([
                        'message'   => 'Sorry there\'s an error deleting user role. Please try again.'
                    ], 500 );
                }
            } else {
                return response([
                    'message'   => 'This user role is not deletable.'
                ], 401 );
            }
        } else {
            return response([
                'message'   => 'You are not allowed here.'
            ], 401 );
        }
    }
}
