<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: black !important;
    font-weight: normal !important;
}

#receipt-table_length,
#receipt-table_filter,
#receipt-table_info,
.paginate_button {
    color: white !important;
}

#eodSummaryListModal-table_paginate,
#eodSummaryListModal-table_previous,
#eodSummaryListModal-table_next,
#eodSummaryListModal-table_length,
#eodSummaryListModal-table_filter,
#eodSummaryListModal-table_info {
    color: white !important;
}

.paging_full_numbers a.paginate_button {
    color: #fff !important;
}

.paging_full_numbers a.paginate_active {
    color: #fff !important;
}

table.dataTable th.dt-right,
table.dataTable td.dt-right {
    text-align: right !important;
}

td {
    vertical-align: middle !important;
    text-align: center;
}

.bg-fuel-refund {
    color: white !important;
    border-color: #ff7e30 !important;
    background-color: #ff7e30 !important;
}
.boxhead a:hover {
    text-decoration: none;
}

.bg-total{
    background-color: rgb(255,126,48) !important;

}
.modal-inside .row {
    padding: 0px;
    margin: 0px;
    color: #000;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 0px !important;
}

table.dataTable.display tbody tr.odd>.sorting_1 {
    background-color: unset !important;
}

tr:hover,
tr:hover>.sorting_1 {
    background: none !important;
}

table.dataTable.display tbody tr.odd>.sorting_1,
table.dataTable.order-column.stripe tbody tr.odd>.sorting_1 {
    background: none !important;
}
table, th, td {
  border: 0.3px solid rgb(243, 237, 237) !important;
  border-collapse: collapse !important;
}
button:focus{
    outline: 0;
}
button:hover{
     background: #1c9939 !important;
     color: #fff;
}
table.dataTable.order-column tbody tr>.sorting_1,
table.dataTable.order-column tbody tr>.sorting_2,
table.dataTable.order-column tbody tr>.sorting_3,
table.dataTable.display tbody tr>.sorting_1,
table.dataTable.display tbody tr>.sorting_2,
table.dataTable.display tbody tr>.sorting_3 {
    background-color: #fff !important;
}
label {
    float: left;
}
table ,tr,td{
    padding:0.25% !important;
}
span {
    display: block;
    overflow: hidden;
    padding: 0px 4px 0px 6px;
}

input {
    width: 70%;
}

input.number {
	text-align: center;
	border: none;
	border: 1px solid #e2dddd;
	margin: 0px;
	width: 90px;
	border-radius: 5px;
	height: 38px;
	border-radius: 5px;
	background-color: #d4d3d36b !important;
	vertical-align: text-bottom;
}

.text-center.no-pad.dt-center.vt_middle.small-pad-y.sorting {
    /* padding: 5px 7px; */
}
.text-center.dt-right.vt_middle.center-text.sorting_desc,
.text-center.center-text.dt-right.vt_middle.sorting {
    text-align: center!important;
}

.dt-center.vt_middle.small-pad-y {
    padding-top: 0%;
    padding-bottom: 0%;
}

</style>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		<div class="d-flex"
			style="width:100%;margin-bottom: 5px; margin-top:5px">
			<div class="col-md-8 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Receiving Note</h2>
			</div>

			<div class="row col-md-4 text-left m-0 pr-0">
                <div class="col-md-10 align-items-center d-flex pr-0" style="justify-content:flex-end">
                    <label class="mb-0">Invoice No&nbsp;</label>
                    <span>
                    <input name="invoice" class="form-control">
                    </span>
                </div>
                <div class="col-md-2 d-flex pr-0 pl-0"
					style="justify-content:flex-end">
                    <button class="btn btn-success sellerbutton mr-0 btn-sq-lg bg-confirm-button"
					onclick="update_quantity()"
					style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Confirm
				</button>
                </div>

			</div>
		</div>
         <div style="margin-top: 0">
            <table border="0" cellpadding="0" cellspacing="0" class="table " id="rec_note_table" style="margin-top: 0px; width:100%">
                <thead class="thead-dark"  >
                <tr id="table-th" style="border-style: none">
                    <th valign="middle" class="text-center" style="width:5%">No</th>
                    <th valign="middle" class="text-center" style="width:15%;">Product ID</th>
                    <th valign="middle" class="text-left" style="width:50%;">Product Name</th>
                    <th valign="middle" class="text-center" style="width:10%;">Price</th>
                    <th valign="middle" class="text-center no-pad" style="width:10%;">Qty</th>
                    <th valign="middle" class="text-center" style="width:10%;">Cost</th>
                    <th valign="middle" class="text-center" style="width:10%;">Cost Value</th>
                </tr>
                </thead>
                <tbody >
                <?php $__currentLoopData = $receivingnotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="">
                        <td class="text-center" style="border-style: none">
                            <?php echo e($k + 1); ?>

                        </td>
                        <td class="text-center" style="border-style: none;width:15%;" >
                            <span id="product_<?php echo e($k +1); ?>" ><?php echo e($product->systemid); ?></span>
                        </td>
                        <td class="text-left" style="border-style: none;">
                        <img src="/images/product/thumb/<?php echo e($product->thumbnail_1); ?>" alt="imf" style="height:25px;width:25px;"> <?php echo e($product->name); ?>

                        </td>
                        <td class="text-center" style="border-style: none;width:10%;" id="price_<?php echo e($k +1); ?>">
                            <?php echo e(number_format($product->Iprice / 100,2)); ?>

                        </td>

                        <td class=" dt-right vt_middle slim-cell">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="align-self-center value-button increase" id="increase_<?php echo e($k + 1); ?>" onclick="increaseValue('<?php echo e($k + 1); ?>')" value="Increase Value">
                                        <ion-icon class="ion-ios-plus-outline" style="font-size: 24px;margin-right:5px;">
                                        </ion-icon>
                                    </div>

                                    <input type="number" id="number_<?php echo e($k + 1); ?>" oninput="changeValueOnBlur('<?php echo e($k + 1); ?>')" class="number product_qty js-product-qty" value="<?php echo e($product->Iqty); ?>" min="0" required="">

                                    <div class="value-button decrease" id="decrease_<?php echo e($k + 1); ?>" onclick="decreaseValue('<?php echo e($k + 1); ?>')" value="Decrease Value">
                                        <ion-icon class="ion-ios-minus-outline" style="font-size: 24px;margin-left:5px;">
                                        </ion-icon>
                                    </div>
                                </div>
                        </td>
                        <td class="text-center" style="border-style: none;width:10%;" id="cost_<?php echo e($k + 1); ?>">
                            <?php echo e(number_format($product->Icost / 100,2)); ?>

                        </td>
                        <td class="text-center" style="border-style: none;width:10%;" id="costvalue_<?php echo e($k +1); ?>">
                            <?php echo e(number_format($product->Icostvalue / 100,2)); ?>

                        </td>
                        

                    </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        		</tbody>
             </table>
         </div>
	</div>
<script src="<?php echo e(asset('js/number_format.js')); ?>"></script>
<script>


let loyalty = "loyalty";
let qty = "qty";

var received_products = {}

var tbl_url = "<?php echo e(route('receiving_notes.get_datatable_products')); ?>";
var tbl_data = {};
/*
var rec_note_table = $('#rec_note_table').DataTable({
    "processing": false,
    "serverSide": true,
    "autoWidth": false,
    "ajax": {
        "url": tbl_url,
		"type": "POST",
		data: function() {
			 return tbl_data
		},
		'headers': {
			'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
		}
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        // {data: 'product.systemid', name: 'systemid'},
        {
            data: 'product.systemid', name: 'systemid', render: function (data) {
                return "<a>" + data + "</a>"
            }
        },
        {
            data: 'product', name: 'product_name', render: function (data) {
                let img = "<img src='$img_src' data-field='inven_pro_name' style=' width: 25px;" +
						"height: 25px;display: inline-block;margin-right: 8px;object-fit:contain;'>"
                let prod_name = "<a >  " + (data.name == null ? 'Product name' : data.name) + "</a>";
                return img + prod_name
            }
        },
        {
            data: 'price', name: 'price', render: function (data) {
                return "<a>" + number_format((JSON.parse(data)["price"] != null ? JSON.parse(data)["price"] : 0) / 100, 2) + "</a>"
            }
        },
        {
            data: 'action', name: 'action'
        },
        {
            data: 'cost', name: 'cost', render: function (data) {
                let systemid = JSON.parse(data)["product"]["systemid"]; // product id
                return "<a style='align:left;text-decoration: none;'>" + number_format((JSON.parse(data)["cost"] != null ? JSON.parse(data)["cost"] : 0) / 100, 2) + "</a>"
            }
        },
        {
            data: 'cost_value', name: 'cost_value', render: function (data) {
                let systemid = JSON.parse(data)["product"]["systemid"];
                return "<a id='cv_" + systemid + "' style='text-decoration: none;'>" + number_format((JSON.parse(data)["cost_value"] != null ? JSON.parse(data)["cost_value"] : 0) / 100, 2) + "</a>"
            }
        },
    ],

});*/

	function increaseValue(id) {

		var num_element = document.getElementById('number_'+id);
		var value = parseFloat(num_element.value);
        var costvalue = document.getElementById('costvalue_'+id);
         var cost = document.getElementById('cost_'+id);

		value = isNaN(value) ? 0 : value;
		value++;
		 num_element.value = value;
         costvalue.textContent = num_element.value  * parseInt(cost.innerHTML.trim().replaceAll(',', ''));

	}


	function decreaseValue(id) {
		var num_element = document.getElementById('number_'+id);
         var costvalue = document.getElementById('costvalue_'+id);
         var cost = document.getElementById('cost_'+id);

		var value = parseFloat(num_element.value);
			value = isNaN(value) ? 0 : value;
			value < 1 ? value = 1 : '';
			value--;
		 num_element.value = value;
         costvalue.textContent = value * parseInt(cost.innerHTML.trim().replaceAll(',', ''));

	}

	function changeValueOnBlur(id) {
		x = 0;
		ele = document.querySelectorAll('.number');
		ele.forEach( (e) => x += parseInt(e.value));
        var num_element = document.getElementById('number_'+id)

       return num_element.value;
	}

function add_scanned_barcode(e, keys) {
	console.log(keys.join(""));
	search_product_barcode(keys.join(""))
	keys.length = 0;
	flag = 0;
	index = 0;
}

function search_product_barcode(product_barcode) {
	tbl_url = "<?php echo e(route('receiving_notes.search_barcode')); ?>";
	tbl_data = {
		'barcode': product_barcode,
	}
	rec_note_table.ajax.url( tbl_url ).load();
}
function update_quantity() {
        // const btn = document.getElementById('confirm');

        // btn.addEventListener('click', function onClick() {
        // btn.style.backgroundColor = '#A8A8A8';
        // btn.style.color = 'white';
        // });



        let dataLength = "<?php echo e(count($receivingnotes) +1); ?>";
        let container = [];
        let product = {};
        console.log(parseInt(dataLength));

        for(let i=1; i < parseInt(dataLength); i++) {
             let price = document.getElementById("price_"+i);
             let product_id = document.getElementById('product_'+i);
             var num_element = document.getElementById('number_'+i);
              var cost = document.getElementById('cost_'+i);
              var costvalue = document.getElementById('costvalue_'+i);

             product.price =  parseInt(price.innerHTML.trim().replaceAll(',', ''));
             product.product_id = product_id.textContent;
             product.qty =  parseInt(num_element.value.trim().replaceAll(',', ''));
             product.cost =  parseInt(cost.innerHTML.trim().replaceAll(',', ''));
             product.costvalue =  parseInt(costvalue.innerHTML.trim().replaceAll(',', ''));
             container.push(product);
             product ={};

        }

		if (container.length < 1)
			return;

		$.ajax({
		  url: "<?php echo e(route('update_receive_notes')); ?>",
		  type: "POST",
		  'headers': {
				'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
			},
		  data: {container},
		  cache: false,
             success: function(dataResult){
			  //$("#productResponse").html(dataResult);
			messageModal(`Receivied note created successfully`);
			tablestockin.ajax.reload();
		  }
		});
	}
var keys = [];
var index = 0;

$(document).ready(function() {
    rec_note_table.ajax.reload();
    window.addEventListener("keyup",
	function(e){
		//console.log(e.keyCode);
		if(e.keyCode != 16 ){
			if( e.keyCode != 13){
				keys[index++] = e.key;
			}else {
				add_scanned_barcode(e, keys);
			}
		}
	}, false
	);
});

</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/user/oceania/trunk/oceania/resources/views/receiving_note/receiving_note.blade.php ENDPATH**/ ?>