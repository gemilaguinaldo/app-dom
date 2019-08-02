<?php

namespace App\Http\Controllers\API;

use DB;
use Validator;
use App\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ExpensesController extends Controller {
    public function list() {
        $user = Auth::user();
        $expenses = Expense::query();

        $result = $expenses
                    ->where( 'user_id', $user->id )
                    ->orderBy( 'entry_date', 'desc' )
                    ->get();

        return response([
            'expenses'     => $result
        ], 200 );
    }

    public function add( Request $request ) {
        $user = Auth::user();

        if ( $request->all() ) {
            $rules = array(
                'category'      => 'required',
                'amount'        => 'required',
                'entry_date'    => 'required'
            );

            $validator = Validator::make( Input::all(), $rules );

            if ( $validator->fails() ) {
                return response( $validator->messages(), 422 );
            } else {
                $insert_expense = Expense::create([
                    'user_id'       => $user->id,
                    'category'      => $request->category,
                    'amount'        => $request->amount,
                    'entry_date'    => $request->entry_date,
                ]);
    
                if ( $insert_expense ) {
                    return response([
                        'message'   => 'Expense has been added.'
                    ], 200 );
                } else {
                    return response([
                        'message'   => 'Sorry there\'s a problem on creating expense. Please try again.'
                    ], 500 );
                }
            }
        }
    }

    public function update( Request $request ) {
        $expense_id = $request->expense_id;

        $expense                = Expense::find( $expense_id );
        $expense->category      = $request->category;
        $expense->amount        = $request->amount;
        $expense->entry_date    = $request->date;
        $update_expense         = $expense->save();

        if ( $update_expense ) {
            return response([
                'message'   => 'Expense has been updated.'
            ], 200 );
        } else {
            return response([
                'message'   => 'Sorry there\'s an error updating expense. Please try again.'
            ], 500 );
        }
    }

    public function delete( Request $request ) {
        $expense_id = $request->expense_id;

        $expense = Expense::find( $expense_id );
        $delete_expense = $expense->delete();

        if ( $delete_expense ) {
            return response([
                'message'   => 'Expense has been deleted.'
            ], 200 );
        } else {
            return response([
                'message'   => 'Sorry there\'s an error deleting expense. Please try again.'
            ], 500 );
        }
    }

    public function total() {
        $user = Auth::user();
        $expenses = Expense::query();

        $result = $expenses
                    ->select( 'category', DB::raw( 'sum(expenses.amount) as total' ) )
                    ->where( 'user_id', $user->id )
                    ->groupBy( 'category' )
                    ->get();

        return response([
            'expenses'  => $result
        ], 200 );
    }
}
