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
a{
    text-decoration: none !important;
}



.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: black;
}

th, td {
    vertical-align: middle !important;
    text-align: center
}

label, .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #000 !important;
}

.active_button {
    color: #ccc;
    border: 1px #ccc solid;
}

.active_button:hover, .active_button:active,
.active_button_activated {
    background: transparent !important;
    color: #34dabb !important;
    border: 1px #34dabb solid !important;
    font-weight: bold;
}

.active_button_activated {
    background: transparent;
    color: #34dabb;
    border: 1px #34dabb solid;
    font-weight: bold;
}

.table th, .table td {
	vertical-align: top;
	border: 1px solid #dee2e6;
}

.slim-cell {
    padding-top: 2px !important;
    padding-bottom: 2px !important;
}

.pd_column {
    padding-top: 10px;
}


.typewriter {
    text-align: right;
    border-radius: 3px;
    overflow: hidden; /* Ensures the content is not revealed until the animation */
    /* border-right: .15em solid black;*/ /* The typwriter cursor */
    white-space: nowrap; /* Keeps the content on a single line */
    margin: 0 auto; /* Gives that scrolling effect as the typing happens */
    letter-spacing: .0em; /* Adjust as needed */
    /* animation:
      typing 3.5s steps(40, end),
      blink-caret .75s step-end infinite;*/
}

/* The typewriter cursor effect */
@keyframes  blink-caret {
    from, to {
        border-color: transparent
    }
    50% {
        border-color: black;
    }
}

#tb_pr.text-center.dt-right.vt_middle.sorting,
#tb_ct'
#tb_cv {
	text-align: center !important;
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
                    <input id="note_invoice" name="invoice" class="form-control">
                    </span>
                </div>
                <div class="col-md-2 d-flex pr-0 pl-0"
					style="justify-content:flex-end">
                    <button id="confirm_update" class="btn btn-success sellerbutton mr-0 btn-sq-lg bg-confirm-button"
					onclick="update_quantity()"
					style="margin-bottom:0 !important;border-radius:10px; cursor:not-allowed;font-size:14px;" disabled>Confirm
				</button>
                </div>

			</div>
		</div>
         <div style="margin-top: 0">
            <table border="0" cellpadding="0" cellspacing="0" class="table " id="rec_note_table" style="margin-top: 0px; width:100%">
                <thead class="thead-dark"  >
                <tr id="table-th" style="border-style: none">
                    <th valign="middle" class="text-center" style="width: 30px;text-align: center !important;">No</th>
                    <th valign="middle" class="text-center" style="width: 160px;text-align: center !important;">Product ID</th>
                    <th valign="middle" class="text-left" style="text-align: left !important;">Product Name</th>
                    <th valign="middle" class="text-center" style="width: 100px;text-align: center !important;">Price</th>
                    <th valign="middle" class="text-center no-pad" style="width: 140px;text-align: center !important;">Qty</th>
                    <th valign="middle" class="text-center" style="width: 100px;text-align: center !important;">Cost</th>
                    <th valign="middle" class="text-center" style="width: 100px;text-align: center !important;">Cost Value</th>
                </tr>
                </thead>
                <tbody id="shows">
            	</tbody>
             </table>
         </div>
	</div>

<div class="modal fade" id="update_cost_modal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered"
		 role="document" style="width: 300px">
		<div class="modal-content ">

			<div class="modal-body bg-purplelobster">
				<div class='text-center ' style="margin:auto">

					<div class="typewriter"
						id="retail_cost_fk_text"
						style="padding: 6px 12px 6px 12px;
						background-color: white; color: #0c0c0c; ">0.00
					</div>

					<input type="text" id="retail_cost_fk"
						style="text-align:right; margin-top: -14%; opacity: 0"
						class="form-control"
						placeholder='0.00' value="0"/>

					<input type="hidden" id="retail_cost_fk_buffer"
						style="text-align:right; " class="form-control"
						placeholder='' value="0"/>

					<input type="hidden" id='retail_cost'/>
					<input type="hidden" id="element_price"
						style="text-align:right" value=""
						class="form-control" placeholder='0'/>
                    <input type="hidden" id="pro_id"
                        style="text-align:right" value=""
                        class="form-control" placeholder='0'/>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo e(asset('js/number_format.js')); ?>"></script>
<script>
$(document).ready(function() {
    
} );

var stockin_updates = [];

function atm_money(num) {
    if (num.toString().length == 1) {
        return '0.0' + num.toString()
    } else if (num.toString().length == 2) {
        return '0.' + num.toString()
    } else if (num.toString().length == 3) {
        return num.toString()[0] + '.' + num.toString()[1] +
            num.toString()[2];
    } else if (num.toString().length >= 4) {
        return num.toString().slice(0, (num.toString().length - 2)) +
            '.' + num.toString()[(num.toString().length - 2)] +
            num.toString()[(num.toString().length - 1)];
    }
}
        

function add_cost_modal(data, old_value , pro, record_id) {
	update_record_id = record_id
	
	console.log('**update_record_id**', update_record_id)
    $("#update_cost_modal").modal("show");
    if (parseInt(old_value) > 0) {
        $("#retail_cost_fk").val(atm_money(old_value));
        $("#retail_cost_fk_text").text(atm_money(old_value));
    } else {
        $("#retail_cost_fk").val('');
        $("#retail_cost_fk_text").text("0.00");
    }
    $("#retail_cost").val(old_value);
    $("#element_price").attr("value", data);
    $("#pro_id").attr("value", pro);
}

$("#retail_cost_fk").on("keyup keypress", function (evt) {
    let old_value = "";
    let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

    if (evt.type === "keypress") {
        let value = $("#retail_cost_fk").val();
        console.log("value", value);
        old_value = parseInt(value.replace('.', ''));
        $("#retail_cost").val(old_value == '' ? 0 : old_value);
    } else {
        if (evt.key === "Backspace") {
            let value = $("#retail_cost_fk").val();
            console.log("value", value);
            old_value = parseInt(value.replace('.', ''));
            $("#retail_cost").val(old_value);
        }

        let use_key = "";
        if (type_evt_not_use.includes(evt.key)) {
            use_key = evt.key;
            console.log(evt.key);
        }

        old_value = parseInt((isNaN($("#retail_cost").val()) == false ? $("#retail_cost").val() : 0) + "" + use_key);
        let nan = isNaN(old_value);
        console.log("up", old_value);

        if (old_value !== "" && nan == false) {
            $("#retail_cost_fk").val(atm_money(parseInt(old_value)));
            $("#retail_cost_fk_text").text(atm_money(parseInt(old_value)));
            $("#retail_cost").val(parseInt(old_value));
        } else {
            $("#retail_cost_fk").val("0.00");
            $("#retail_cost_fk_text").text("0.00");
            $("#retail_cost").val(0);
        }
    }
});

$('#update_cost_modal').on('hidden.bs.modal', function (e) {
    let key = "price";
    let value = $("#retail_cost").val();
    let element = $("#element_price").val();
    
    console.log("key =", key);
    console.log("value =", value);
    console.log("element =", element);
    
    $.ajax({
        method: "post",
        url: "<?php echo e(route('openitem.update_cost')); ?>",
        data: {
        	record_id: update_record_id,
        	new_cost: value,
        }
    })
    .done((data) => {

        console.log("data", data);
         payload = {
		    id: $('#pro_id').val()
		};
		console.log("data", payload);
		localStorage.removeItem('update_product');
		localStorage.setItem("update_product",JSON.stringify(payload));
		openitemCostTable.ajax.reload();
		update_record_id = 0
    })
    .fail((data) => {
        console.log("data", data)
    });
});

	
let loyalty = "loyalty";
let qty = "qty";

var received_products = {}



	setInterval(() => {
		if (stockin_updates.length > 0) {
			$("#confirm_update").removeAttr('disabled');	
			$("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
			$("#confirm_update").css('cursor','pointer');
		} else {
			$("#confirm_update").attr('disabled', true);	
			$("#confirm_update").css('background','gray');
			$("#confirm_update").css('cursor','not-allowed');
		}
	}, 1500);
	
	

function increaseValue(id) {

	var num_element = document.getElementById('number_'+id);
	var value = parseFloat(num_element.value);
	var costvalue = document.getElementById('costvalue_'+id);
	var cost = document.getElementById('cost_'+id);
	

	value = isNaN(value) ? 0 : value;
	value++;
	num_element.value = value;

	let cost_value = num_element.value * (cost.innerHTML *100)

	costvalue.textContent = number_format((cost_value/100),2)
	
	if(value > 0 ) {
		let product_id = $('#product_'+id).data('prd_id');
		// Gat values/objects that do not include current product
		stockin_updates = stockin_updates.filter(function(me) {
			if(me.product_id != product_id) {
				return me;
			}
		});
		
		let price = document.getElementById("price_"+id);
		//let product_id = $('#product_'+id).data('prd_id');
		let ptype = $('#product_'+id).data('ptype');
		let qty = document.getElementById('number_'+id);
		let cost = document.getElementById('cost_'+id);
		let costvalue = document.getElementById('costvalue_'+id);

		stockin_updates.push({
			'price': price.innerHTML,
			'product_id': product_id,
			'ptype': ptype,
			'qty': qty.value,
			'cost': cost.innerHTML,
			'costvalue': costvalue.innerHTML,
			'tag_id': id
		});
	}

}

function decreaseValue(id) {
	var num_element = document.getElementById('number_'+id);
	var value = parseFloat(num_element.value);
	var costvalue = document.getElementById('costvalue_'+id);
	var cost = document.getElementById('cost_'+id);

	
	value = isNaN(value) ? 0 : value;
	value < 1 ? value = 1 : '';
	value--;
	num_element.value = value;

	let cost_value = num_element.value * (cost.innerHTML *100)

	costvalue.textContent = number_format((cost_value/100),2)
	
	if(value > 0 ) {
		let product_id = $('#product_'+id).data('prd_id');
		// Gat values/objects that do not include current product
		stockin_updates = stockin_updates.filter(function(me) {
			if(me.product_id != product_id) {
				return me;
			}
		});
		
		let price = document.getElementById("price_"+id);
		let prd_type = $('#product_'+id).data('prd_type');
		let qty = document.getElementById('number_'+id);
		let cost = document.getElementById('cost_'+id);
		let costvalue = document.getElementById('costvalue_'+id);

		stockin_updates.push({
			'price': price.innerHTML,
			'product_id': product_id,
			'ptype': ptype,
			'qty': qty.value,
			'cost': cost.innerHTML,
			'costvalue': costvalue.innerHTML,
			'tag_id': id
		});
	
	}
}

function changeValueOnBlur(id) {
	console.log()
	x = 0;
	ele = document.querySelectorAll('.number');
	ele.forEach( (e) => x += parseInt(e.value));

	var num_element = document.getElementById('number_'+id)
	
	let update_cv = document.getElementById('costvalue_'+id);
    var cost = document.getElementById('cost_'+id);
    
    let new_cv = num_element.value * (cost.innerHTML *100)
	 
    update_cv.textContent = number_format((new_cv/100),2)
    
    ele.forEach( (e) => {
		
		if(e.value > 0 ) {
		
			var main_id = e.id.replace('number_','');
			
			let price = document.getElementById("price_"+id);
			let product_id = $('#product_'+id).data('prd_id');
			let prd_type = $('#product_'+id).data('prd_type');
			let qty = document.getElementById('number_'+id);
			let cost = document.getElementById('cost_'+id);
			let costvalue = document.getElementById('costvalue_'+id);
			
			// Gat values/objects that do not include current product
			stockin_updates = stockin_updates.filter(function(me) {
				if(me.product_id != product_id) {
					return me;
				}
			});

			stockin_updates.push({
				'price': price.innerHTML,
				'product_id': product_id,
				'prd_type': prd_type,
				'qty': qty.value,
				'cost': cost.innerHTML,
				'costvalue': costvalue.innerHTML,
				'tag_id': id
			});
		}
	  
	});
     
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
	
	
	if (stockin_updates.length > 0) {
	
		let inv_no = document.getElementById("note_invoice")
		stockin_updates.forEach((prd) => {
			prd.invoice_no = inv_no.value
			container.push(prd);
		});

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
				messageModal(`Received note created successfully`);
				
				
				stockin_updates = []
				rec_note_table.ajax.reload();
			}
		});
	}
}
	
var keys = [];
var index = 0;


var tableData = {
    systemid: "<?php echo e(request()->route('systemid')); ?>"
};

var tbl_url = "<?php echo e(route('receiving_notes.get_datatable_products')); ?>";
var tbl_data = {};

var rec_note_table = $('#rec_note_table').DataTable({
    "processing": false,
    "serverSide": true,
    "autoWidth": false,
    "ajax": {
        /* This is just a sample route */
        "url": "<?php echo e(route('receiving_notes.datatable')); ?>",
        "type": "POST",
        data: function (d) {
            return $.extend(d, tableData);
        },
        'headers': {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        error: function (xhr, error, code)
        {
            console.log(xhr);
            console.log(code);
        },
    },
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex'},
        { data: 'systemid', name: 'systemid' },
        { data: 'product_name', name: 'product_name' },
        { data: 'product_price', name: 'product_price' },
        { data: 'qty', name: 'qty' },
        { data: 'cost', name: 'cost' },
        { data: 'costvalue', name: 'costvalue' },
    ],
    // "order": [0, 'desc'],
    "columnDefs": [
        {"width": "30px", "targets": [0]},
        {"width": "160px", "targets": [1]},
        {"width": "140px", "targets": [4]},
        {"width": "100px", "targets": [3,4,5,6]},
        {"className": "dt-center vt_middle", "targets": [1]},
        {"className": "dt-left vt_middle", "targets": [2]},
        {"className": "dt-center vt_middle px-2", "targets": [4]},
        {"className": "dt-right vt_middle", "targets": [3,5,6]},
        // {"className": "dt-right vt_middle", "targets": [2]},
    ],
    "drawCallback": function( settings ) {
		
		    if (stockin_updates.length > 0) {
		    	stockin_updates.forEach((prd) => {
		    		let input_id = 'number_'+prd.tag_id
		    		$('#'+input_id).val(prd.qty)
				});
		    }
	 
		}
});


$(document).ready(function() {

	$("#confirm_update").attr('disabled', true);	
	$("#confirm_update").css('background','gray');
	$("#confirm_update").css('cursor','not-allowed');
			
    // rec_note_table.ajax.reload();
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
	}, false);
});

</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/receiving_note/receiving_note.blade.php ENDPATH**/ ?>