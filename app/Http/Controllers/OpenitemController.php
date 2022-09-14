<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Http\Controllers\SyncSalesController;
use App\Models\Company;
use App\Models\Location;
use App\Models\MerchantPrdCategory;
use App\Models\MerchantProduct;
use App\Models\PrdOpenitem;
use App\Models\PrdPrdCategory;
use App\Models\PrdSubCategory;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Milon\Barcode\DNS1D;
use Yajra\DataTables\DataTables;

class OpenitemController extends Controller
{
    public static $IMG_PRODUCT_LINK = "images/product/";
    public function openitem()
    {
        try {

            $test = null;

            return view(
                'openitem.openitem_landing',
                compact('test')
            );
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function save()
    {
        try {
            $data = array();
            // WARNING: Hardcoding location_id=1
            $systemid = SystemID::openitem_system_id(1);
            $merchant = DB::table('company')->first();
            $product = Product::create([
                "systemid" => $systemid,
                "name" => null,
                'ptype' => 'openitem',
            ]);

            $prdOpenitem = PrdOpenitem::create([
                "product_id" => $product->id,
                "price" => 0.00,
                "qty" => 0,
                "loyalty" => $merchant->loyalty_pgm,
            ]);

            if (Auth::user() != null) {
                $merchant_pdr = MerchantProduct::create([
                    "product_id" => $product->id,
                    "merchant_id" => $merchant->id,
                ]);
                $data['merchant_pdr'] = DB::table('merchantproduct')->
                    whereId($merchant_pdr->id)->first();
            }

            //gather open item data
            $data['prd_openitem'] = DB::table('prd_openitem')->
                whereId($prdOpenitem->id)->first();

            $data['product'] = DB::table('product')->
                whereId($product->id)->first();

            //send the data to OCOSYSTEM
            SyncSalesController::curlRequest(
                env('MOTHERSHIP_URL') . '/sync-openitem',
                json_encode($data)
            );

            return [
                "data" => $prdOpenitem,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function get_latest_cost_openitem_landing_tbl($data)
    {

        $updated_data = collect();

        foreach ($data as $prd) {
            $prd_info = DB::table('product')->
                whereId($prd->product_id)->
                whereNUll('deleted_at')->
                first();

            $prd->cost = 0;

            if (!empty($prd_info)) {
                $latest_cost = DB::table('openitem_productledger')->
                    where('product_systemid', $prd_info->systemid)->
                    whereNotNull('cost')->
                    orderBy('created_at', 'desc')->
                    first();

                $prd->cost = empty($latest_cost) ? 0 : $latest_cost->cost;
            }
            $updated_data->push($prd);
        }
        return $updated_data;
    }

    public function listPrdOpenitem()
    {
        try {
            PrdOpenitem::get()->map(function ($f) {
                $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($f->product_id);
                $f->update();
            });

            $data = PrdOpenitem::has('product')->
                with("product")->select('*')->get();

            $data = $this->get_latest_cost_openitem_landing_tbl($data);

            Log::debug("OpenitemController::listPrdOpenitem()==>data= " .
                json_encode($data));

            foreach ($data as $prd) {
                $prd->cost_value = $prd->cost * $prd->qty;

                $transaction = DB::table('cstore_receiptproduct')->
                    where('product_id', $prd->product_id)->first();

                $stock = DB::table('stockreportproduct')->
                    where('product_id', $prd->product_id)->first();

                if (empty($transaction) && empty($stock)) {
                    $prd->can_delete = true;
                } else {
                    $prd->can_delete = false;
                }
            }

            return Datatables::of($data)->addIndexColumn()->
                editColumn('cost', function ($data) {
                $cost = $data;
                return $cost;
            })->editColumn('qty', function ($data) {
                $qty = $data;
                return $qty;
            })->editColumn('cost_value', function ($data) {
                $cost_value = DB::select(
                    DB::raw("
						select sum(op.cost * op.balance) as total from openitem_cost op, product p, openitem_productledger opl where p.systemid = opl.product_systemid and opl.id = op.openitemprodledger_id and p.id = '" . $data->product_id . "'
					")
                );

                if (!empty($cost_value)) {
                    $total = $cost_value[0]->total;
                } else {
                    $total = 0;
                }

                DB::table('prd_openitem')->where(
                    'id',
                    $data->id
                )->update([
                    'costvalue' => $total,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return is_null($total) ? number_format(0, 2) :
                number_format($total / 100, 2);

            })->editColumn('loyalty', function ($data) {
                $loyalty = $data;
                return $loyalty;
            })->editColumn('royalty', function ($data) {
                $royalty = $data;
                return $royalty;
            })->editColumn('price', function ($data) {
                $price = $data;
                return $price;
            })->addColumn('barcode', function ($data) {
                $product_id = $data->product_id;
                $rec = DB::table('productbarcode')
                    ->where('product_id', $product_id)
                    ->where('selected', 1)
                    ->first();

                if (is_null($rec)) {
                    $product = DB::table('product')
                        ->where('id', $product_id)
                        ->first();
                    return $product->systemid;
                } else {
                    return $rec->barcode;
                }
            })
                ->addColumn('action', function ($row) {

                    if ($row->can_delete == true) {
                        $btn = '<a  href="javascript:void(0)" onclick="deleteMe(' .
                        $row->id . ',' . $row->product_id . ')" data-row="' .
                        $row->id . '" class="delete"> <img width="25px" src="images/redcrab_50x50.png" alt=""> </a>';
                        return $btn;
                    }
                    $btn = '<a  style="text-decoration: none;  filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" class="delete"> <img width="25px" src="images/redcrab_50x50.png" alt=""> </a>';
                    return $btn;
                })->rawColumns(['action'])->make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function detailProduct(Request $request)
    {
        try {
            $product_details = Product::whereId($request->id)->first();
            /*
            $product_category = PrdCategory::all();
            $product_brand = PrdBrand::all();
            $product_subcategory = PrdSubCategory::all();
            $product_product = PrdPrdCategory::all();
             */
            return view(
                "openitem.product_details",
                compact("product_details")
            );
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function updateCustom(Request $request)
    {
        try {
            $data = [
                "name" =>
                $request->product_name == null ?
                null : $request->product_name,
            ];

            if (Auth::user() != null) {
                $merchant = DB::table('company')->first();
                MerchantPrdCategory::create([
                    "category_id" => 0,
                    "merchant_id" => $merchant->id,
                ]);
            }

            $prd = Product::where(
                "systemid",
                $request->systemid
            )->update($data);

            return [
                "data" => $prd,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function get_dropDown($OPTION, $KEY)
    {
        $data = [];
        if ($OPTION == "subcat") {
            $data = PrdSubCategory::where("category_id", $KEY)->get();
        } else {
            $data = PrdPrdCategory::where("subcategory_id", $KEY)->get();
        }

        return $data;
    }

    public function delPicture(Request $request)
    {
        try {
            $data = [
                "thumbnail_1" => null,
                "photo_1" => null,
            ];

            $prd = Product::where(
                "systemid",
                $request->systemid
            )->update($data);

            return [
                "data" => $prd,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function savePicture(Request $request)
    {
        try {

            if ($request->file != null) {
                $filename = $this->generatePhotoName(
                    $request->file->getClientOriginalExtension()
                );

                $request->file->move(public_path(self::$IMG_PRODUCT_LINK .
                    $request->product_id . "/"), $filename);

                $path = public_path(self::$IMG_PRODUCT_LINK . $request->product_id . "/") . "thumb/";
                if (!file_exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $thumb_path = public_path(self::$IMG_PRODUCT_LINK . $request->product_id . "/") .
                    "thumb/" . "thumb_" . $filename;

                File::copy(
                    public_path(self::$IMG_PRODUCT_LINK . $request->product_id . "/" . $filename),
                    $thumb_path
                );

                $img = Image::make($thumb_path);
                $img->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumb_path);

                $data["photo_1"] = $filename;
                $data["thumbnail_1"] = "thumb_" . $filename;
            }

            Product::where("systemid", $request->product_id)->update($data);

            $prd = Product::where("systemid", $request->product_id)->first();

            return [
                "name" => $prd->name,
                "src" => self::$IMG_PRODUCT_LINK .
                $request->product_id . "/" . $filename,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function updateOpen(Request $request)
    {
        $data = [
            $request->key => $request->value,
        ];

        $prdOpen = PrdOpenitem::where("id", $request->element)->update($data);

        return [
            "data" => $prdOpen,
            "error" => false,
        ];
    }

    public function save_prd_cost(Request $request)
    {
        try {
            $product = DB::table('product')
                ->where('systemid', $request->product_id)
                ->select('id')->first();

            if (!empty($product)) {
                DB::table('prd_openitem')->where(
                    'product_id',
                    $product->id
                )->update([
                    'cost' => $request->cost_amount,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                Log::error([
                    'Message' => "Product Cost Update failed. Product Not found",
                    'ProductId' => $request->product_id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
        }
    }

    public function deleteOpen(Request $request)
    {
        $prdOpen = PrdOpenitem::where("id", $request->id)->first();
        if (!empty($prdOpen)) {
            Product::find($prdOpen->product_id)->delete();
            PrdOpenitem::find($request->id)->delete();
            $ret = ["data" => $prdOpen, "error" => false];
        } else {
            $ret = ["data" => [], "error" => true];
        }
        return $ret;
    }

    public function generatePhotoName($ext)
    {
        return "p" . time() . "-m" . $this->generateRandomString(14) . "." . $ext;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function prdLedger($systemid)
    {
        try {

            $product = Product::where("systemid", $systemid)->first();

            $location = Location::first();
            $data = collect();

            DB::table('cstore_receipt')->
                select('cstore_receipt.*', 'cstore_receiptproduct.quantity as quantity',
                'cstore_receipt.id as show_receipt_id', 'cstore_receiptdetails.id as receiptdetails_id')->
                join('cstore_receiptproduct', 'cstore_receipt.id', 'cstore_receiptproduct.receipt_id')->
                // join('locprod_productledger', 'locprod_productledger.product_id', 'cstore_receiptproduct.product_id')->
                leftJoin('cstore_receiptdetails', 'cstore_receipt.id', 'cstore_receiptdetails.receipt_id')->
                orderBy('cstore_receipt.updated_at', "desc")->
                where("cstore_receiptproduct.product_id", $product->id)->

                get()->
                map(function ($product) use ($data) {
                $packet = collect();
                $packet->id = $product->id;
                $packet->show_receipt_id = $product->show_receipt_id;
                $packet->status = $product->status;
                $packet->systemid = $product->systemid;
                $packet->quantity = $product->quantity * -1;
                //$packet->cost       = $product->cost;
                $packet->created_at = $product->created_at;
                $packet->voided_at = $product->voided_at;
                $packet->doc_type = "Cash Sales";
                $data->push($packet);
            });

            DB::table('stockreportproduct')->
                leftjoin('stockreport', 'stockreport.id', 'stockreportproduct.stockreport_id')->
                // leftjoin('locprod_productledger', 'locprod_productledger.product_id', 'stockreportproduct.product_id')->
                where('stockreportproduct.product_id', $product->id)->
                orderBy('stockreport.updated_at', "desc")->
                get()->map(function ($product) use ($data) {
                $packet = collect();
                $packet->id = $product->id;
                $packet->status = $product->status;
                $packet->systemid = $product->systemid;
                $packet->quantity = $product->quantity;
                // $packet->cost    = $product->cost;
                $packet->created_at = $product->created_at;
                $packet->voided_at = $product->voided_at ?? "";
                $packet->doc_type = ucfirst($product->type);
                $data->push($packet);
            });

            $data = $this->add_cost_to_prd_ledger($data, $product->id);

            /* Here you store the $data into a storage table:
            openitem_productledger */

            $data = $data->sortByDesc('created_at')->values();

            Log::debug(['Sorted Data:=' => $data]);

            return view(
                'openitem.openitem_productledger',
                compact('product', 'data', 'location')
            );
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function add_cost_to_prd_ledger($data, $prd_id)
    {

        $prd_info = DB::table('product')->whereId($prd_id)->first();

        $new_data = collect();

        foreach ($data as $prd) {

            $cost = DB::table('openitem_productledger')->
                where('product_systemid', $prd_info->systemid)->
                where('qty', $prd->quantity)->
                where('stockreport_id', $prd->id)->
                orderBy('created_at', 'desc')->
                first();

            if (!empty($cost)) {
                $prd->cost = $cost->cost;
            } else {
                $prd->cost = 0;
            }

            $new_data->push($prd);
            $this->reflect_autostock_openitemproduct_cost();
        }

        return $new_data;
    }

    public function openitemStockout()
    {
        try {

            $location = DB::table('location')->first();
            return view('openitem.openitem_stockout', compact('location'));
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function openitemStockin()
    {
        try {

            $location = DB::table('location')->first();
            return view('openitem.openitem_stockin', compact('location'));
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function openitem_stock_update(Request $request)
    {
        Log::debug('****OpenItem Stock Update()*****');
        try {
            $user_id = \Auth::user()->id;
            $table_data = $request->get('table_data');
            $stock_type = $request->get('stock_type');
            $stock_system = new SystemID("stockreport");

            $company = Company::first();
            $location = Location::first();

            foreach ($table_data as $key => $value) {

                //if qty zero
                if ($value['qty'] == 0) {
                    continue;
                }
                //If SI or SO
                if ($stock_type == "IN") {
                    $curr_qty = $value['qty'];
                    $type = 'stockin';
                } else {
                    $curr_qty = $value['qty'] * -1;
                    $type = 'stockout';
                }

                // Openitem Product
                $openitemproduct = DB::table('prd_openitem')->where([
                    'product_id' => $value['product_id'],
                ])->first();

                if ($openitemproduct) { // modify existing openitem product

                    $openitemproduct = DB::table('prd_openitem')->where([
                        'product_id' => $value['product_id'],
                    ])->increment('qty', $curr_qty);

                } else {
                    DB::table('prd_openitem')->insert([
                        "product_id" => $value['product_id'],
                        "qty" => $curr_qty,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                //Stock Report
                $stockreport_id = DB::table('stockreport')->insertGetId([
                    'systemid' => $stock_system,
                    'creator_user_id' => $user_id,
                    'type' => $type,
                    'location_id' => $location->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('stockreportproduct')->insert([
                    "stockreport_id" => $stockreport_id,
                    "product_id" => $value['product_id'],
                    "quantity" => $curr_qty,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $prd = DB::table('product')->
                    whereId($value['product_id'])->
                    first();

                if ($stock_type == "IN") {
                    $latest_cost = DB::table('openitem_productledger')->
                        where('product_systemid', $prd->systemid)->
                        whereIn('type', ['stockin', 'received'])->
                        whereNotNull('cost')->
                        orderBy('created_at', 'desc')->
                        first();

                    $cost = empty($latest_cost) ? 0 : $latest_cost->cost;

                    $openitemprodid = DB::table('openitem_productledger')->
                        insertGetId([
                        "stockreport_id" => $stockreport_id,
                        "product_systemid" => $prd->systemid,
                        "qty" => $curr_qty,
                        "cost" => $cost,
                        "last_update" => date('Y-m-d H:i:s'),
                        "status" => 'active',
                        "type" => $type,
                        "deleted_at" => null,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $cost_id = DB::table('openitem_cost')->
                        insertGetId([
                        "openitemprodledger_id" => $openitemprodid,
                        "cost" => $cost,
                        "qty_in" => $curr_qty,
                        "qty_out" => 0,
                        "balance" => $curr_qty,
                        "deleted_at" => null,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                } else if ($stock_type == "OUT") {
                    $earliest_cost = DB::table('openitem_productledger')->
                        where('product_systemid', $prd->systemid)->
                        whereNotNull('cost')->
                        orderBy('created_at', 'asc')->
                        first();

                    $cost = empty($earliest_cost) ? 0 : $earliest_cost->cost;

                    $openitemprodid = DB::table('openitem_productledger')->
                        insertGetId([
                        "stockreport_id" => $stockreport_id,
                        "product_systemid" => $prd->systemid,
                        "qty" => $curr_qty,
                        "cost" => $cost,
                        "last_update" => date('Y-m-d H:i:s'),
                        "status" => 'active',
                        "type" => $type,
                        "deleted_at" => null,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $this->process_openitem_stockout($prd->systemid, $curr_qty);
                    Log::debug('****OpenItemcost csreceipt Update()*****');
                    $this->create_receiptcost();
                }

                PrdOpenitem::where('product_id', $value['product_id'])->get()->map(function ($f) {
                    $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->
                        qtyAvailable($f->product_id);
                    $f->update();
                });

            }
            return response()->json(["status" => true]);
        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }

    public function process_openitem_stockout($systemid, $curr_qty)
    {

        try {
            // Get oldest non zero balance
            $oldest_bal = DB::table('openitem_cost')->
                join(
                'openitem_productledger',
                'openitem_productledger.id',
                'openitem_cost.openitemprodledger_id'
            )->
                select(
                'openitem_productledger.id as ledger_id',
                'openitem_productledger.stockreport_id as sr_id',
                'openitem_productledger.type as doc_type',
                'openitem_cost.cost as cost',
                'openitem_cost.id as id',
                'openitem_cost.qty_in as qty_in',
                'openitem_cost.qty_out as qty_out',
                'openitem_cost.balance as balance',
                'openitem_cost.created_at as created_at',
                'openitem_cost.updated_at as updated_at')->
                where("openitem_productledger.product_systemid", $systemid)->
                where('openitem_cost.balance', '>', 0)->
                orderBy('openitem_cost.created_at', 'asc')->
                first();

            Log::debug("OpenitemController::process_openitem_stockout::Oldest balance: " . json_encode($oldest_bal));

            $cost = empty($oldest_bal) ? 0 : $oldest_bal->cost;

            if (!empty($oldest_bal)) {
                $compare = $curr_qty;
                if ($oldest_bal->balance >= ($compare * -1)) {
                    DB::table('openitem_cost')->
                        whereId($oldest_bal->id)->
                        update([
                        "qty_out" => $curr_qty + $oldest_bal->qty_out,
                        "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;

                    DB::table('openitem_cost')->
                        whereId($oldest_bal->id)->
                        update([
                        "qty_out" => $oldest_bal->qty_in * -1,
                        "balance" => 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $this->process_openitem_stockout($systemid, $carry_over_bal);
                }
            }

            $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            //abort(500);
        }
    }

    public function stockOutList()
    {
        try {

            $product_data_open_item = DB::table('product')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                whereNotNull(['product.name',
                // 'product.thumbnail_1
            ])->where('prd_openitem.qty', '>', '0')->select("product.*", "prd_openitem.price as recommended_price")->get();

            $product_data_open_item = $product_data_open_item->filter(function ($product) {
                return app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product->id) > 0;
            });

            return Datatables::of($product_data_open_item)->
                addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                return $data->systemid;

            })->addColumn('product_name', function ($data) {
                $img_src = '/images/product/' .
                $data->systemid . '/thumb/' .
                $data->thumbnail_1;

                if (!empty($data->thumbnail_1) &&
                    file_exists(public_path() . $img_src)) {

                    $img = "<img src='$img_src' data-field='inven_pro_name'
						style=' width: 25px; height: 25px;
						display: inline-block;margin-right: 8px;
						object-fit:contain;'>";

                } else {
                    $img = '';
                }

                return $img . $data->name;

            })->addColumn('product_qty', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product_id);
                return <<<EOD
						<span id="qty_$product_id">$qty</span>
EOD;
            })->addColumn('action', function ($data) {
                $product_id = $data->id;
                return view('fuel_stockmgmt.inven_qty', compact('product_id'));
            })->rawColumns(['action'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function stockInList()
    {
        try {

            $product_data_open_item = DB::table('product')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                whereNotNull([
                'product.name',
                //'product.thumbnail_1'
            ])->select("product.*", "prd_openitem.price as recommended_price")->get();

            return Datatables::of($product_data_open_item)->addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                return $data->systemid;
            })->addColumn('product_name', function ($data) {
                $img_src = '/images/product/' .
                $data->systemid . '/thumb/' .
                $data->thumbnail_1;

                if (!empty($data->thumbnail_1) &&
                    file_exists(public_path() . $img_src)) {

                    $img = "<img src='$img_src' data-field='inven_pro_name'
						style=' width: 25px; height: 25px;
						display: inline-block;margin-right: 8px;
						object-fit:contain;'>";

                } else {
                    $img = '';
                }

                return $img . $data->name;
            })->addColumn('product_qty', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product_id);
                return <<<EOD
						<span id="qty_$product_id">$qty</span>
EOD;
            })->addColumn('action', function ($data) {
                $product_id = $data->id;
                return view('fuel_stockmgmt.inven_qty', compact('product_id'));
            })->rawColumns(['action'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function product_has_qty(Request $request)
    {
        try {
            $product = DB::table('prd_openitem')->
                where('product_id', $request->product_id)->first();

            if (!empty($product) && $product->qty > 0) {
                return response()->json([
                    'has_qty' => true,
                ]);
            }
            return response()->json([
                'has_qty' => false,
            ]);
        } catch (\Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
        }
    }

    public function product_barcode(Request $request)
    {
        $system_id = $request->route('systemid');

        $product = DB::table('product')->
            join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
            where('product.systemid', $system_id)->first();

        $product_id = $product->id;
        return view(
            'openitem.openitem_barcode',
            compact('system_id', 'product', 'product_id')
        );
    }

    public function delete_product_barcode(Request $request)
    {

        $prd_barcode = DB::table('productbarcode')->
            where("barcode", $request->barcode)->
            whereNull('deleted_at')->first();

        if (!empty($prd_barcode)) {
            DB::table('productbarcode')->
                where('barcode', $request->barcode)->
                whereNull('deleted_at')->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            $ret = ["data" => $prd_barcode, "error" => false];
        } else {
            $ret = ["data" => [], "error" => true];
        }
        return $ret;
    }

    public function show_barcode_table(Request $request)
    {
        try {
            $product = DB::table('prd_openitem')->
                join('product', 'prd_openitem.product_id', 'product.id')->
                where('prd_openitem.id', $request->prd_id)
                ->first();

            $barcode = [];

            $default = collect();

            $default['systemid'] = $product->systemid;

            $barcode[] = $default;

            $productbarcode = DB::table('productbarcode')->
                where('product_id', $product->id)->
                whereNull('deleted_at')->orderBy('id', 'desc')->get();

            // Prepend a new record at the start of the collection
            $o = new \stdClass();
            $o->id = $product->id;
            $o->product_id = $product->id;
            $o->barcode = $product->systemid;
            $productbarcode->prepend($o);

            Log::debug('product_barcode_datatable:  AFTER productbarcode=' .
                json_encode($productbarcode));

            $productbarcode->map(function ($f) use ($barcode) {
                if (!empty($barcode[0][0])) {

                    Log::debug('product_barcode_datatable map(): 2. barcode=' .
                        json_encode($barcode));

                    $product_barcode = collect();

                    Log::debug('product_barcode_datatable map(): product_barcode=' .
                        json_encode($product_barcode));

                    $product_barcode['systemid'] = $f->barcode;

                    $notes = $product_barcode->notes;
                    $notes .= "Start Date: <b>";
                    $notes .= date("dMy", strtotime($f->startdate));
                    $notes .= "</b><br>";
                    $notes .= "Expiry Date: <b>";
                    $notes .= date("dMy", strtotime($f->expirydate));
                    $notes .= "</b>";
                    $product_barcode['notes'] = $notes;

                    $barcode[] = $barcode;
                }
            });

            Log::debug('product_barcode_datatable: 3. barcode=' .
                json_encode($barcode));

            return Datatables::of($productbarcode)->
                addIndexColumn()->
                addColumn('product_barcode', function ($memberList) {

                Log::debug('product_barcode_datatable: memberList=' .
                    json_encode($memberList));

                /*
                Log::debug('product_barcode_datatable: systemid='.
                $memberList->systemid.', barcode='.
                $memberList->barcode);
                 */

                $code = new DNS1D();
                $code = $code->getBarcodePNG(trim($memberList->barcode), "C128");
                $bc = $memberList->barcode;

                return <<<EOD
                        <div style="display:flex;justify-content: flex-start;">
                            <div style="display:flex;flex-direction: column;justify-content: center;align-items: center;">
                                <img src="data:image/png;base64,$code" style="display:block;"
                                     alt="barcode" class="" width="200px" height="70px "/>
                                $bc
                            </div>
                        </div>
      EOD;
            })->addColumn('select', function ($row) {
                // This is is for the "Display" column
                if (isset($row->merchantproduct_id)) {
                    if ($row->selected) {
                        return '
                    <label class="containerx" style="margin-left:20px;padding-bottom:10px;">
                        <input type="checkbox" checked="checked" onchange="select_barcode(' . $row->id . ', ' . $row->product_id . ')">
                        <span class="checkmark"></span>
                    </label>
                    ';
                    } else {
                        return '
                    <label class="containerx" style="margin-left:20px;padding-bottom:10px;">
                        <input type="checkbox" onchange="select_barcode(' . $row->id . ', ' . $row->product_id . ')">
                        <span class="checkmark"></span>
                    </label>
                    ';
                    }
                }

            })
                ->addColumn('action', function ($row) {
                    $transaction = DB::table('cstore_receiptproduct')->
                        where('product_id', $row->product_id)->first();

                    if (empty($transaction)) {
                        $row->can_delete = true;
                    } else {
                        $row->can_delete = false;
                    }

                    $prd_sysid = DB::table('product')->
                        whereId($row->product_id)->first();

                    if ($row->barcode == $prd_sysid->systemid) {
                        $btn = '<a  style="text-decoration: none;  filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" ' .
                            ' class="delete"> ' .
                            '</a>';
                        return $btn;
                    } else if ($row->can_delete == true) {
                        $btn = '<a  href="javascript:void(0)" id="bc_' .
                        $row->barcode . '"' .
                        ' onclick="delete_barcode(this.id)" data-barcode="' .
                        $row->barcode . '" class="delete"> ' .
                            ' <img width="25px" src="/images/redcrab_50x50.png" alt=""> ' .
                            '</a>';
                        return $btn;
                    } else {
                        $btn = '<a  style="text-decoration: none;  filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" ' .
                            ' class="delete"> ' .
                            '<img width="25px" src="/images/redcrab_50x50.png" alt=""> ' .
                            '</a>';
                        return $btn;
                    }
                })->rawColumns(['action'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function select_barcode_record(Request $request)
    {
        $product_id = $request->product_id;
        $barcode_id = $request->barcode_id;

        DB::table('productbarcode')
            ->where('selected', 1)
            ->where('product_id', $product_id)
            ->update(['selected' => 0]);

        DB::table('productbarcode')
            ->where('id', $barcode_id)
            ->where('product_id', $product_id)
            ->update(['selected' => 1]);

        return response(['status' => 1], 200);
    }

    public function save_barcode(Request $request)
    {
        $barcodes = trim($request->barcodes);
        $barcodes = str_replace("\n", ";", $barcodes);
        $barcodes = str_replace(",", ";", $barcodes);
        $parts = explode(';', $barcodes);

        Log::debug('parts=' . json_encode($parts));

        $company = Company::first();
        $merchant_id = $company->id;
        $duplicate_barcodes = "";

        $prd = DB::table('prd_openitem')->
            whereId($request->id)->first();

        $merchant_product = DB::table('merchantproduct')->
            select('id')->
            where('merchant_id', '=', $merchant_id)->
            where('product_id', '=', $prd->product_id)->first();

        Log::debug('merchant_product=' . json_encode($merchant_product));

        $is_duplicate = false;
        foreach ($parts as $part) {
            $part = trim($part);

            Log::debug('merchant_id=' . $merchant_id);
            Log::debug('product_id =' . $request->id);
            Log::debug('barcode    =' . $part);

            $count = DB::table('merchantproduct as mp')->join(
                'productbarcode as pb',
                'mp.id',
                '=',
                'pb.merchantproduct_id'
            )->select('pb.barcode')->where('mp.merchant_id', '=', $merchant_id)->
                // where('mp.product_id', '=', $request->id)->
                where('pb.barcode', '=', $part)->whereNull('pb.deleted_at')->count();

            Log::debug('count=' . json_encode($count));

            if (empty($count)) {
                DB::table('productbarcode')
                    ->where('product_id', $prd->product_id)
                    ->where('selected', 1)
                    ->update(['selected' => 0]);
                DB::table('productbarcode')->insert([
                    "merchantproduct_id" => $merchant_product->id,
                    "product_id" => $prd->product_id,
                    "selected" => 1,
                    "barcode" => $part,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $is_duplicate = true;
                $duplicate_barcodes .= $part . "<br>";
            }
        }

        if ($is_duplicate) {
            $msg = "Duplicated barcodes found:<br>" .
                $duplicate_barcodes;

            // $html = view('openitem.dialog', compact('msg'))->render();
            return $msg;
        } else {
            return 0;
        }
    }

    /**
     * Create Barcode from user input range
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create_barcode_from_input_range(Request $request)
    {
        $this->barcodeGeneratorValidator($request);

        $barcode_from = (int) $request->get('barcode_from');
        $barcode_to = (int) $request->get('barcode_to');
        $product_id = $request->get('product_id');
        $barcode_notes = $request->get('barcode_notes');
        $merchant_id = (new UserData())->company_id();

        $merchant_product = DB::table('merchantproduct')->
            select('id')->
            where('merchant_id', '=', $merchant_id)->
            where('product_id', '=', $product_id)->first();

        if ($barcodes = $this->checkIfBarcodesExistWithRange(
            $product_id, $merchant_product->id,
            $barcode_from, $barcode_to)) {

            $unique_barcodes = array_unique($barcodes);
            sort($unique_barcodes);
            if (count($unique_barcodes) > 10) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'System detected clashing barcodes already in existence: <div class="text-left"><br/>'
                    . implode('<br/>', array_slice($unique_barcodes, 0, 10)) .
                    '<br>Another ' . (count($unique_barcodes) - 10) .
                    ' barcodes existed.</div>',
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'System detected clashing barcodes already in existence: <div class="text-left"><br/>'
                    . implode('<br/>', array_slice($unique_barcodes, 0, 10)) .
                    '</div>',
                ]);
            }
        }

        $this->createMultipleBarcodesWithRanges($barcode_from, $barcode_to, [
            'barcode_type' => 'C128',
            'product_id' => $product_id,
            'merchantproduct_id' => $merchant_product->id,
            'notes' => $barcode_notes,
        ]);

        $this->update_barcode_oceania($product_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Barcode generated successfully',
        ]);
    }

    public function generate_bar_code(Request $request)
    {
        try {
            $bm_id = $request->bm_id;
            $changed = false;
            //bmatrixbarcode
            $bmatrix = DB::table('bmatrix')->
                where('id', $bm_id)->
                whereNull('deleted_at')->first();

            if (empty($bmatrix)) {
                throw new \Exception("Invalid barcode matrix");
            }

            $attributes = DB::table('bmatrixattrib')->
                where('bmatrix_id', $bm_id)->
                whereNull('deleted_at')->
                whereNotNull('name')->get();

            $bmatrixcolor = DB::table('bmatrixcolor')->
                where('bmatrix_id', $bm_id)->
                where([['color_id', '!=', 0]])->
                whereNull('deleted_at')->get();

            $attrib_items = DB::table('bmatrixattribitem')->whereIn(
                'bmatrixattrib_id',
                $attributes->pluck('id')
            )->whereNull('deleted_at')->whereNotNull('name')->get();

            $color_items = DB::table('color')->
                whereIn('id', $bmatrixcolor->pluck('color_id'))->
                //    whereNotIn('hex_code', ["#000","#000000"])->
                whereNull('deleted_at')->pluck('id');
            $array = [];

            foreach ($attributes as $a) {
                $array[] = DB::table('bmatrixattribitem')->
                    where('bmatrixattrib_id', $a->id)->
                    whereNull('deleted_at')->pluck('id');
            }
            $combined_attr = $this->combos($array);
            $z_array = [];

            if (count($color_items) > 0) {
                foreach ($color_items as $c) {
                    if (count($combined_attr) > 0) {
                        foreach ($combined_attr as $a) {
                            $a['color'] = $c;
                            $z_array[] = $a;
                        }
                    } else {
                        $z_array = array(['color' => $c]);
                    }
                }
            } else {

                foreach ($combined_attr as $a) {
                    $a['color'] = 0;
                    $z_array[] = $a;
                }
            }

            $is_exist = DB::table('bmatrixbarcode')
                ->where('bmatrix_id', $bmatrix->id)
                ->whereNull('deleted_at')->get();

            if ($is_exist->count() > 0) {

                DB::table('bmatrixbarcode')
                    ->where('bmatrix_id', $bmatrix->id)
                    ->delete();
            }

            foreach ($z_array as $barcode) {
                $bar = [];
                foreach ($barcode as $key => $id) {
                    if ($key === 'color') {
                        if ($id != 0) {
                            $bmatrixcolor_id = DB::table('bmatrixcolor')->
                                where('color_id', $id)->first()->id;

                            $bar[] = ["color_id" => $bmatrixcolor_id];
                        } else {
                            $bar[] = ["color_id" => 0];
                        }
                    } else {
                        $bmatrixattribitem = DB::table('bmatrixattribitem')->
                            where('id', $id)->first();
                        $bmatrixattrib = DB::table('bmatrixattrib')->
                            where('id', $bmatrixattribitem->bmatrixattrib_id)->first();
                        $bar[] = [$bmatrixattrib->id => $bmatrixattribitem->id];
                    }
                }

                ///        $bar[] = ["bmatrix_id" => $bmatrix->id];
                $string = json_encode($bar);

                DB::table('bmatrixbarcode')->insert([
                    "bmatrix_id" => $bmatrix->id,
                    "pbarcode" => $string,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date("Y-m-d H:i:s"),
                ]);
            }

            if ($is_exist->count() > 0) {
                $ischanged = DB::table('bmatrixbarcode')->
                    where('bmatrix_id', $bmatrix->id)->
                    whereNotIn('pbarcode', $is_exist->
                        pluck('pbarcode')->toArray())->
                    whereNull('deleted_at')->get();

                if ($ischanged->count() > 0) {
                    $this->remove_exisiting_switchs($bmatrix->id);
                }
            }
            $msg = "Definition updated successfully";
            return view('layouts.dialog', compact('msg'))->render();
        } catch (\Exception $e) {
            Log::info($e);
            abort(404);
        }
    }

    public function openitem_cost(Request $request)
    {
        $prd_sysid = $request->systemid;
        $prd_info = DB::table('product')->
            where('systemid', $prd_sysid)->
            first();
        $this->reflect_autostock_openitemproduct_cost();
        $this->create_receiptcost();
        return view('openitem.openitem_cost', compact('prd_sysid', 'prd_info'));
    }

    public function openitem_cost_datatable(Request $request)
    {
        try {
            $prd = DB::table('product')->
                where('systemid', $request->systemid)->
                first();

            $cost_data = DB::table('openitem_productledger')->
                join('product', 'product.systemid', 'openitem_productledger.product_systemid')->
                join('openitem_cost', 'openitem_cost.openitemprodledger_id', 'openitem_productledger.id')->
                select(
                'openitem_productledger.id as record_id',
                'openitem_productledger.stockreport_id as sr_id',
                'openitem_productledger.type as doc_type',
                'openitem_cost.id as op_id',
                'openitem_cost.cost as cost',
                'openitem_cost.qty_in as stockin',
                'openitem_cost.qty_out as stockout',
                'openitem_cost.balance as balance',
                'openitem_cost.created_at as created_at',
                'openitem_cost.updated_at as updated_at',
                'product.id as product_id')->
                where('openitem_productledger.product_systemid', $request->systemid)->
                orderBy('openitem_productledger.created_at', 'DESC')->
                get();

            $updated_data = collect();

            foreach ($cost_data as $data) {
                if ($data->doc_type == 'stockin') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Stock In';
                } elseif ($data->doc_type == 'received') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Received';
                } elseif ($data->doc_type == 'stockout') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Stock Out';
                }
                $updated_data->push($data);
            }

            return Datatables::of($updated_data)->
                addIndexColumn()->
                addColumn('doc_no', function ($data) {
                $prd_cost = empty($data->cost) ? 0 : $data->cost;

                $doc_no = $data->doc_no;

                if ($data->doc_type == 'Received') {
                    $doc_no = '<a  href="javascript:window.open(\'' . route('receiving_list_id', $data->doc_no) . '\')" style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                } elseif ($data->doc_type == 'Stock In') {
                    $doc_no = '<a  href="javascript:window.open(\'' . route('stocking.stock_report', $data->doc_no) . '\')" style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                } elseif ($data->doc_type == 'Stock Out') {
                    $doc_no = '<a  href="javascript:window.open(\'' . route('stocking.stock_report', $data->doc_no) . '\')" style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                }

                return $doc_no;
            })->
                addColumn('type', function ($data) {
                return $data->doc_type;
            })->
                addColumn('cost_date', function ($data) {
                $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                return $created_at;
            })->
                addColumn('cost', function ($data) {
                $prd_cost = empty($data->cost) ? 0 : $data->cost;

                if (empty($data->stockout) || $data->stockout == 0) {
                    $cost = '<a  href="javascript:void(0)"  onclick="open_update_cost_modal(' . $data->product_id . ',' . $prd_cost . ',' . $data->record_id . ',' . $data->record_id . ')" data-prod="' . $data->product_id . '" style="text-decoration:none;" class="">' . number_format($data->cost / 100, 2) . ' </a>';
                } else {
                    $cost = '<a style="text-decoration:none;" class="">' . number_format($data->cost / 100, 2) . ' </a>';
                }

                return $cost;
            })->
                addColumn('stockin', function ($data) {
                return $data->stockin;
            })->
                addColumn('stockout', function ($data) {

                $so_qty = empty($data->stockout) ? 0 : $data->stockout * -1;

                if (empty($so_qty) || $so_qty == 0) {
                    return $so_qty;
                } else {
                    return '<a href="#" onclick="show_doc_qty_modal(' . $data->stockout . ', ' . $data->op_id . ')" style="text-decoration:none;" class="">' . $so_qty . ' </a>';
                }
            })->
                addColumn('balance', function ($data) {
                return $data->balance;
            })->
                escapeColumns([])->
                make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function openitem_update_cost(Request $request)
    {
        try {
            DB::table('openitem_productledger')->
                whereId($request->record_id)->
                update([
                'cost' => $request->new_cost,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table('openitem_cost')->
                where('openitemprodledger_id', $request->record_id)->
                update([
                'cost' => $request->new_cost,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json(["status" => true]);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function openitem_qty_population_datatable(Request $request)
    {
        try {
            $op_id = $request->op_id;
            $qty_population = DB::select(DB::raw("
                SELECT
                    opc.csreceipt_id,
                    opc.qty_taken,
                    cr.systemid
                FROM openitem_cost oc
                JOIN openitemcost_csreceipt opc ON opc.openitemcost_id = oc.id
                JOIN cstore_receipt cr ON cr.id = opc.csreceipt_id
                WHERE oc.id = $op_id;
            "));

            $res = [
                'records' => sizeof($qty_population),
                'data' => $qty_population,
            ];

            return response($res, 200);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function reflect_autostock_openitemproduct_cost()
    {

        $opnProd = DB::select(
            DB::raw("

            SELECT
                p.id as product_id,
                p.name,
                p.systemid,
                srp.quantity,
                srp.stockreport_id
            FROM
                product p,
                prd_openitem op,
                stockreport sr,
                stockreportproduct srp
            WHERE
                op.product_id = p.id AND
                srp.product_id = p.id AND
                srp.stockreport_id = sr.id
            ;")
        );

        if (!empty($opnProd)) {

            foreach ($opnProd as $key => $value) {
                # code...
                $stocrep = DB::table('openitem_productledger')->
                    where('stockreport_id', $value->stockreport_id)->
                    where('product_systemid', $value->systemid)->first();
                $product = DB::table('openitem_productledger')->
                    where('product_systemid', $value->systemid)->
                    latest()->
                    first();

                if (!$stocrep) {

                    if ($product) {

                        $cost = DB::select(
                            DB::raw(
                                "
                            SELECT
                                opl.product_systemid,
                                max(opc.cost) as locost
                            FROM
                                openitem_productledger opl,
                                openitem_cost opc
                            WHERE
                                opc.openitemprodledger_id ='" . $product->id . "'
                            GROUP BY
                                opl.product_systemid
                                ;"
                            )
                        );

                        $cost = $cost[0]->locost ?? 0;
                        Log::debug("LOCost: " . $cost);
                        if ($value->quantity >= 0) {
                            $lpl_id = DB::table('openitem_productledger')->insertGetID([
                                "stockreport_id" => $value->stockreport_id,
                                "product_systemid" => $value->systemid,
                                "qty" => $value->quantity,
                                "cost" => $cost,
                                "status" => "active",
                                "type" => "stockin",
                                "last_update" => date('Y-m-d H:i:s'),
                                "created_at" => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                            DB::table('openitem_cost')->insert([
                                "openitemprodledger_id" => $lpl_id,
                                "cost" => $cost,
                                "qty_in" => $value->quantity,
                                "qty_out" => $value->quantity * -1,
                                "balance" => ($value->quantity * -1) + $value->quantity,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                        }
                    } else {

                        if ($value->quantity >= 0) {
                            $lpl_id = DB::table('openitem_productledger')->insertGetID([
                                "stockreport_id" => $value->stockreport_id,
                                "product_systemid" => $value->systemid,
                                "qty" => $value->quantity,
                                "cost" => 0,
                                "status" => "active",
                                "type" => "stockin",
                                "last_update" => date('Y-m-d H:i:s'),
                                "created_at" => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                            DB::table('openitem_cost')->insert([
                                "openitemprodledger_id" => $lpl_id,
                                "qty_in" => $value->quantity,
                                "qty_out" => $value->quantity * -1,
                                "balance" => ($value->quantity * -1) + $value->quantity,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function create_receiptcost()
    {
        Log::debug("Started create_receiptcost.");
        $receipt_id = DB::select(
            DB::raw("
            SELECT
                opc.id as openitemcost_id,
                opc.cost,
                opc.qty_in,
                opc.qty_out as qty,
                opc.balance,
                cr.id as csreceipt_id,
                crp.quantity as qty
            FROM
                openitem_cost opc,
                openitem_productledger opl,
                stockreport sr,
                stockreportproduct srp,
                cstore_receiptproduct crp,
                cstore_receipt cr
            WHERE
                opc.openitemprodledger_id = opl.id AND
                opl.stockreport_id = srp.stockreport_id AND
                opl.stockreport_id = sr.id AND
                srp.product_id = crp.product_id AND
                crp.receipt_id = cr.id
            ;")
        );

        // $receipt_id = DB::select(
        //     DB::raw("
        //     SELECT
        //         opc.cost,
        //         opc.qty_in,
        //         opc.qty_out as qty,
        //         opc.balance,
        //         opc.id as openitemcost_id,
        //         crp.receipt_id as csreceipt_id

        //     FROM openitem_cost opc
        //     LEFT JOIN openitem_productledger opl ON opl.id = opc.openitemprodledger_id
        //     LEFT JOIN stockreportproduct srp ON srp.stockreport_id = opl.stockreport_id
        //     LEFT JOIN stockreport sr ON sr.id = srp.stockreport_id
        //     LEFT JOIN product p ON p.id = srp.product_id
        //     LEFT JOIN cstore_receiptproduct crp ON srp.product_id = crp.product_id
        //     LEFT JOIN cstore_receipt cr ON cr.id = crp.receipt_id

        //     WHERE qty_out <> 0;
        //     ")
        // );

        if (!empty($receipt_id)) {
            Log::debug("OpenitemController::create_receiptcost::receipt_id: " . json_encode($receipt_id));
            foreach ($receipt_id as $key => $value) {
                # code...
                $stocrep = DB::table('openitemcost_csreceipt')->
                    where('csreceipt_id', $value->csreceipt_id)->
                    where('openitemcost_id', $value->openitemcost_id)->
                    first();
                Log::debug("OpenitemController::create_receiptcost::stocrep: " . json_encode($stocrep));
                if (!$stocrep) {

                    DB::table('openitemcost_csreceipt')->insert([
                        "csreceipt_id" => $value->csreceipt_id,
                        "openitemcost_id" => $value->openitemcost_id,
                        "qty_taken" => ($value->qty * -1),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }
}
