<?php

namespace App\Http\Controllers\API;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UsersController extends Controller {
    public function login( Request $request ) {
        $email_address = $request->email_address;
        $password = $request->password;

        if ( Auth::attempt( [ 'email_address' => $email_address, 'password' => $password ] ) ) {
            $user = Auth::user();
            $success['token'] = $user->createToken('Portfolio')->accessToken;
            $user->api_token = $success['token'];
            $user->save();

            return response([
                'message'   => 'You have successfully logged in.',
                'token'     => $success['token']
            ], 200 );
        } else {
            return response([
                'message'   => 'Invalid Email Address/Password.'
            ], 400 );
        }
    }

    public function logout() {
        auth()->user()->token()->delete();

        return response([
            'message'   => 'Logged out successfully.'
        ], 200 );
    }

    public function list() {
        $user = Auth::user();
        $users = User::query();

        if ( $user->role == 'Administrator' ) {
            $result = $users
                        ->orderBy( 'created_at', 'asc' )
                        ->get();

            return response([
                'users'     => $result
            ], 200 );
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
                    'name'              => 'required',
                    'email_address'     => 'required|unique:users,email_address,{$id},id,deleted_at,NULL',
                    'role'              => 'required'
                );

                $validator = Validator::make( Input::all(), $rules );

                if ( $validator->fails() ) {
                    return response( $validator->messages(), 422 );
                } else {
                    $insert_user = User::create([
                        'name'              => $request->name,
                        'email_address'     => $request->email_address,
                        'role'              => $request->role,
                        'password'          => bcrypt( 'password' )
                    ]);

                    if ( $insert_user ) {
                        return response([
                            'message'   => 'User has been added.'
                        ], 200 );
                    } else {
                        return response([
                            'message'   => 'Sorry there\'s a problem on creating user. Please try again.'
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
        $user_id = $request->user_id;

        if ( $user->role == 'Administrator' ) {
            if ( $user_id != 1 ) {
                $_user                  = User::find( $user_id );
                $_user->name            = $request->name;
                $_user->email_address   = $request->email_address;
                $_user->role            = $request->role;
                $update_user            = $_user->save();

                if ( $update_user ) {
                    return response([
                        'message'   => 'User has been updated.'
                    ], 200 );
                } else {
                    return response([
                        'message'   => 'Sorry there\'s an error updating user. Please try again.'
                    ], 500 );
                }
            } else {
                return response([
                    'message'   => 'This user is not editable.'
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
        $user_id = $request->user_id;

        if ( $user->role == 'Administrator' ) {
            if ( $user_id != 1 ) {
                $_user = User::find( $user_id );
                $delete_user = $_user->delete();

                if ( $delete_user ) {
                    return response([
                        'message'   => 'User has been deleted.'
                    ], 200 );
                } else {
                    return response([
                        'message'   => 'Sorry there\'s an error deleting user. Please try again.'
                    ], 500 );
                }
            } else {
                return response([
                    'message'   => 'This user is not deletable.'
                ], 401 );
            }
        } else {
            return response([
                'message'   => 'You are not allowed here.'
            ], 401 );
        }
    }

    public function change_password( Request $request ) {
        $user = Auth::user();

        if ( !$request->all() ) {
            $_user              = User::find( $user->id );
            $_user->password    = bcrypt( $request->password );
            $update_user        = $_user->save();

            if ( $update_user ) {
                return response([
                    'message'   => 'Password has been updated.'
                ], 200 );
            } else {
                return response([
                    'message'   => 'Sorry there\'s an error updating password. Please try again.'
                ], 500 );
            }
        }
    }
}
