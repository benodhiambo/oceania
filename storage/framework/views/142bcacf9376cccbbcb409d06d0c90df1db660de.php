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
}

.bg-evreceipt-refund {
	color: white !important;
	border-color: #ff7e30 !important;
	background-color: #ff7e30 !important;
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

table.dataTable.order-column tbody tr>.sorting_1,
table.dataTable.order-column tbody tr>.sorting_2,
table.dataTable.order-column tbody tr>.sorting_3,
table.dataTable.display tbody tr>.sorting_1,
table.dataTable.display tbody tr>.sorting_2,
table.dataTable.display tbody tr>.sorting_3 {
	background-color: #fff !important;
}

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="loadOverlay" style="background-color: white; position:absolute; top:0px; left:0px; 
	width:100%; height:100%; z-index:2000;">
</div>
<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center" style="display:flex;height:75px">
			<div class="col" style="width:70%">
				<h2 style="margin-bottom: 0;">
					Electrical Vehicle Receipt List
				</h2>

			</div>
			<div class="col-md-2">
				<h5 style="margin-bottom:0"></h5>
				<h5 style="margin-bottom:0"></h5>
			</div>
			<div class="middle;col-md-3">
				<h5 style="margin-bottom:0;"></h5>
			</div>
			<div class="col-md-2 text-right">
				<h5 style="margin-bottom:0;"></h5>
			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display" id="evreceiptlist"
			style="width:100%;">
			<thead class="thead-dark">
			<tr>
				<th class="text-center" style="width:30px;">No.</th>
				<th class="text-center" style="width:100px;">Date</th>
				<th class="text-left" style="width:auto;">Receipt ID</th>
				<th class="text-center" style="width:100px;">Total</th>
			</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="evReceiptDetailModal" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered"
		style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

		<!-- Modal content-->
		<div class="modal-content  modal-inside detail_view">
		</div>
	</div>
</div>
<style>
.btn {
	color: #fff !Important;
}

.form-control:disabled, .form-control[readonly] {
	background-color: #e9ecef !important;
	opacity: 1;
}

#void_stamp {
	font-size: 100px;
	color: red;
	position: absolute;
	z-index: 2;
	font-weight: 500;
	margin-top: 130px;
	margin-left: 10%;
	transform: rotate(45deg);
	display: none;
}
</style>
<?php $__env->startSection('script'); ?>

<script src="<?php echo e(asset("js/number_format.js")); ?>"></script>

<script>
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

	
function getEVReceiptDetail(data) {
	$.ajax({
		method: "post",
		url: "<?php echo e(route('ev_receipt.evReceiptDetail')); ?>",
		data: {
			id: data
		}
	}).done((data) => {
		$(".detail_view").html(data);
		$("#evReceiptDetailModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}



$(document).ready(function() {
$("#loadOverlay").css("display","none");
var table = $('#evreceiptlist').DataTable({

"processing": false,
"serverSide": true,
destroy : true,
"autoWidth": false,
"ajax": {
	"url": "<?php echo e(route('ev_receipt.evListData')); ?>",
	"type": "POST",
	data: {
		'date': '<?php echo e($date); ?>',
	},
	'headers': {
		'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
	}
},
"initComplete": function() {
	//alert( 'DataTables has finished its initialisation.' );
},
columns: [{
		data: 'DT_RowIndex',
		name: 'DT_RowIndex'
	},{
		data: 'date',
		name: 'date'
	},{
		data: 'evreceipt_systemid',
		name: 'evreceipt_systemid'
	},{
		data: 'total',
		name: 'total'
	}
],
});
            
        
        });

</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/user/oceania/trunk/oceania/resources/views/ev_receipt/ev_receiptlist.blade.php ENDPATH**/ ?>