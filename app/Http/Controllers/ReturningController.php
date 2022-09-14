<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReturningController extends Controller
{
    //
    public function cstore_returning_list(){
        return view('returning.cstore_returning_list');
    }

    public function cstore_returning(Request $request){


                    $query = DB::table('product')
                            ->join('prd_openitem', function($join){
                               $join->on('product.id','=','prd_openitem.product_id')
                                ->where('prd_openitem.qty','>',0);
                            })
                            ->join('locationproduct', function($join){
                                $join->on('product.id','=','locationproduct.product_id')
                                ->where('locationproduct.quantity','>',0);
                            })
                             ->select('product.name as name','prd_openitem.*','locationproduct.quantity',
                             'locationproduct.cost','locationproduct.costvalue')->get();                          
                      
                      
                  if($request->ajax())
                  {                      
                    $alldata = Datatables::of($query)->addIndexColumn()->make(true);  
                    return $alldata;
                  }
                  
                  return view('returning.cstore_returning',compact('query'));
                       
    }
    
   
}