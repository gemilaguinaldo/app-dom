<?php

namespace App\Http\Controllers\API;

use Validator;
use App\ExpenseCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ExpenseCategoriesController extends Controller {
    public function list() {
        $user = Auth::user();
        $categories = ExpenseCategory::query();

        if ( $_GET['dropdown_list'] != 'true' ) {
            $result = $categories
                ->orderBy( 'created_at', 'asc' )
                ->get();

            return response([
                'categories'     => $result
            ], 200 );
        } else {
            $result = $categories
                ->orderBy( 'created_at', 'asc' )
                ->pluck( 'name' );

            return response([
                'categories'     => $result
            ], 200 );
        }
    }

    public function add( Request $request ) {
        $user = Auth::user();

        if ( $user->role == 'Administrator' ) {
            if ( $request->all() ) {
                $rules = array(
                    'name'          => 'required|unique:expense_categories,name,{$id},id,deleted_at,NULL',
                    'description'   => 'required'
                );

                $validator = Validator::make( Input::all(), $rules );

                if ( $validator->fails() ) {
                    return response( $validator->messages(), 422 );
                } else {
                    $insert_category = ExpenseCategory::create([
                        'name'          => $request->name,
                        'description'   => $request->description
                    ]);
        
                    if ( $insert_category ) {
                        return response([
                            'message'   => 'Category has been added.'
                        ], 200 );
                    } else {
                        return response([
                            'message'   => 'Sorry there\'s a problem on creating category. Please try again.'
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
        $category_id = $request->category_id;

        if ( $user->role == 'Administrator' ) {
            if ( $category_id != 1 ) {
                $category = ExpenseCategory::find( $category_id );
                $category->name = $request->name;
                $category->description = $request->description;
                $update_category = $category->save();
    
                if ( $update_category ) {
                    return response([
                        'message'   => 'Category has been updated.'
                    ], 200 );
                } else {
                    return response([
                        'message'   => 'Sorry there\'s an error updating category. Please try again.'
                    ], 500 );
                }
            } else {
                return response([
                    'message'   => 'This category is not editable.'
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
        $category_id = $request->category_id;

        if ( $user->role == 'Administrator' ) {
            $category = ExpenseCategory::find( $category_id );
            $delete_category = $category->delete();

            if ( $delete_category ) {
                return response([
                    'message'   => 'Category has been deleted.'
                ], 200 );
            } else {
                return response([
                    'message'   => 'Sorry there\'s an error deleting category. Please try again.'
                ], 500 );
            }
        } else {
            return response([
                'message'   => 'You are not allowed here.'
            ], 401 );
        }
    }
}
