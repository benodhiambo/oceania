<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

<style>
<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

<style>

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
</style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div id="landing-view" class="container-fluid">
    <div class="">
		<div class="clearfix"></div>
		<div class="d-flex mt-0 p-0 row" style="width:100%;margin-top:5px !important;margin-bottom:5px !important;height:70px" >
			<div class="col-md-5 align-self-center" style="">
				<h2 style="margin-bottom: 0;">Location Product: Cost</h2>
			</div>

            <div class="col-md-1 align-self-center" style="">
				 <?php if(!empty($prd_info->thumbnail_1) && file_exists(public_path("/images/product/".$prd_info->systemid."/thumb/".$prd_info->thumbnail_1))): ?>
					<img src="/images/product/<?php echo e($prd_info->systemid); ?>/thumb/<?php echo e($prd_info->thumbnail_1); ?>"
						alt="Logo" width="70px" height="70px" alt="Logo"
						style="border-radius:10px;object-fit:contain;float:right;">
				<?php endif; ?>
			</div>

			<div class="col-md-4" style="align-self:center;float:left;padding-left:0">
			   <h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;">
                    <?php if($prd_info->name??""): ?><?php echo e($prd_info->name??""); ?>

                    <?php else: ?> Product Name <?php endif; ?>
                </h4>
                <p style="font-size:18px;margin-bottom:0"><?php echo e($prd_info->systemid??""); ?></p>
			</div>

            <div class="col-2">
            </div>

		</div>

        <div style="margin-top: 0;">
	        <table border="0" cellpadding="0" cellspacing="0" class="table" id="locProdCostTable" style="margin-top: 5px; width:100%">
            <thead class="thead-dark " style="">
            <tr id="table-th" style="border-style: none;">
                <th valign="middle" class="text-center" style="">No</th>
                <th valign="middle" class="text-center"  style="width: 160px;">Document No</th>
                <th valign="middle" class="text-center"  style="width: 100px;">Type</th>
                <th valign="middle" class="text-left" style="">Date</th>
	            <th class="text-center" style="width: 80px;">Cost</th>
				<th class="text-center" style="width: 80px">In</th>
				<th class="text-center" style="width: 80px">Out</th>
				<th class="text-center" style="width: 80px">Balance</th>
            </tr>
            </thead>
             <tbody id="shows">
                </tbody>

        </table>
        </div>
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

<div class="modal fade" id="stockout_qty_modal"  tabindex="-1"
		role="dialog"  aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered modal-md  mw-75 w-50"
		role="document">
		<div class="modal-content modal-inside bg-purplelobster" >
			<div class="modal-header" style="">
				<h4 id="qty_cost_tbl">
					Qty Distribution
				</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered align-content-center"
					id="qty_doc_tbl" style="width:100%">
					<thead class="thead-dark">
						<tr>
							<th style="">Document No.</th>
							<th style="">Qty</th>
						</tr>
					</thead>
					<tbody id="shows">
                    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>




<script>

$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

var tableData = {
    systemid: "<?php echo e(request()->route('systemid')); ?>"
};

var update_record_id = 0

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
   

function open_update_cost_modal(data, old_value , pro, record_id) {
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
        url: "<?php echo e(route('local_price.update_locprd_cost')); ?>",
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
		locProdCostTable.ajax.reload();
		update_record_id = 0
    })
    .fail((data) => {
        console.log("data", data)
    });
});

function show_doc_qty_modal(doc_no) {
	$('#stockout_qty_modal').modal('show');
    show_qty_distrib_tbl(doc_no)
}

function show_qty_distrib_tbl(doc_no) {

    let qty_tbl_data = {
        systemid: "<?php echo e(request()->route('systemid')); ?>",
        doc_no: doc_no 
    }

    let qty_cost_tbl = $('#qty_doc_tbl').DataTable({
        "processing": false,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            /* This is just a sample route */
            "url": "<?php echo e(route('local_price.qty_distrib_datatable')); ?>",
            "type": "POST",
            data: function (d) {
                return $.extend(d, qty_tbl_data);
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
            { data: 'doc_no', name: 'doc_no' },
            { data: 'qty', name: 'qty' },
        ],
        // "order": [0, 'desc'],
        "columnDefs": [
            // {"width": "30px", "targets": [0]},
            // {"className": "dt-center vt_middle", "targets": [2]},
            // {"className": "dt-right vt_middle", "targets": [2]},
        ],
    });
    qty_cost_tbl.ajax.reload();
}


var locProdCostTable = $('#locProdCostTable').DataTable({
    "processing": false,
    "serverSide": true,
    "autoWidth": false,
    "ajax": {
        /* This is just a sample route */
        "url": "<?php echo e(route('local_price.locprod_cost_datatable')); ?>",
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
        { data: 'doc_no', name: 'doc_no' },
        { data: 'doc_type', name: 'doc_type' },
        { data: 'cost_date', name: 'cost_date' },
        { data: 'cost', name: 'cost' },
        { data: 'stockin', name: 'stockin' },
        { data: 'stockout', name: 'stockout' },
        { data: 'balance', name: 'balance' },
    ],
    // "order": [0, 'desc'],
    "columnDefs": [
        {"width": "30px", "targets": [0]},
        {"className": "dt-center vt_middle", "targets": [1]},
        {"className": "dt-center vt_middle", "targets": [2]},
        {"className": "dt-left vt_middle", "targets": [3]},		// Date
        // {"className": "dt-right vt_middle", "targets": [2]},
    ],
});

$(document).ready(function() {
    locProdCostTable.ajax.reload();
});

</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/local_price/prodloc_cost.blade.php ENDPATH**/ ?>