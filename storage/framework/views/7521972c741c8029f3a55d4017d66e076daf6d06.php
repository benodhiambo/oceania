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

.bg-fuel-refund {
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
button:focus{
    outline:  none !important;
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
		<div class="row align-items-center"
			style="display:flex;margin-top:5px;margin-bottom:5px">
			<div class="col d-flex" style="width:70%">
				<h2 style="margin-bottom: 0;">
					Full Tank Receipt List
				</h2>
			</div>
			<div class="col-md-2">
			</div>
			<div class="middle;col-md-3">
			</div>
			<div class="col-md-2 d-flex mb-0"
				style="justify-content:flex-end ">
			<button class="btn btn-success sellerbutton mr-0 mb-0"
				style="height:70px;width:70px; border-radius:10px !important"
				id="fulltank-rl" style="color:white;float:right"
				onclick="window.open('<?php echo e(route('export_excel_fuel_fulltank_receiptlist')); ?>','_blank')">
					<span style="font-size:13px">
					Excel
					</span>
			</button>
			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display" id="eodsummarylistd1" style="width:100%;">
			<thead class="thead-dark">
				<tr>
					<th class="text-center" style="width:30px;">No.</th>
					<th class="text-center" style="width:100px;">Date</th>
					<th class="text-center" style="width:auto;">Receipt ID</th>
					<th class="text-center" style="width:100px;">Total</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="evReceiptDetailModalft" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered"
		style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

		<!-- Modal content-->
		<div class="modal-content  modal-inside detail_view">
		</div>
	</div>
</div>

<?php $__env->startSection('script'); ?>
<script>
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});


function getFuelReceiptlist(data) {
	$.ajax({
			method: "post",
			url: "<?php echo e(route('fulltank.envReceipt')); ?>",
			data: {
				id: data
			}
		}).done((data) => {
			$(".detail_view").html(data);
			$("#evReceiptDetailModalft").modal('show');
		})
		.fail((data) => {
			console.log("data", data)
		});
}

var ft_tbl_url = "<?php echo e(route('ft-fuel-list-table')); ?>";

var ft_tbl_data = {
	'date': '<?php echo e($date); ?>',
}



var table = $('#eodsummarylistd1').DataTable({

	"processing": false,
	"serverSide": true,
	"autoWidth": false,
	"ajax": {
		"url": ft_tbl_url,
		"type": "POST",
		data: function() {
			 return ft_tbl_data
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
	}, {
		data: 'date',
		name: 'date'
	}, {
		data: 'systemid',
		name: 'systemid'
	}, {
		data: 'total',
		name: 'total'
	}],
	"columnDefs": [{
		"width": "3%",
		"targets": [0, 3]
	}, {
		"width": "12%",
		"targets": 1
	}, {
		"className": "dt-center vt_middle",
		"targets": [0, 1, 2, 3]
	}, {
		"className": "vt_middle",
		"targets": [2]
	}, {
		orderable: false,
		targets: [-1]
	}, ],
});


var isVisible;
$(document).ready(function() {
    $("#loadOverlay").css("display","none");

	var hidden, visibilityState, visibilityChange;

	if (typeof document.hidden !== "undefined") {
		hidden = "hidden";
		visibilityChange = "visibilitychange";
		ovisibilityState = "visibilityState";

	} else if (typeof document.msHidden !== "undefined") {
		hidden = "msHidden";
		visibilityChange = "msvisibilitychange";
		visibilityState = "msVisibilityState";
	}

	var document_hidden = document[hidden];

	document.addEventListener(visibilityChange, function() {
		if (document_hidden != document[hidden]) {
			if (document[hidden]) {
				// Document hidden
				console.log("close the tab");
			} else {

				//	window.location.reload();
				table.ajax.reload();
				// updateLocalStorageValues();
				log2laravel('info', 'tab eventlistner :back to tab reloaded');
			}

			document_hidden = document[hidden];
		}
	});

	$('#eodsummarylistd1_filter input').attr ('id', 'ft_fuel_search');

	$('#ft_fuel_search').on( 'keyup', function () {
		let sanitized_search = this.value.replace(/[^a-zA-Z0-9]/g, '')
		sanitized_search.toLowerCase
		if (sanitized_search.charAt(0) == 'p') {
			pump_search(sanitized_search)
		} else {
			ptype_search(sanitized_search)
		}

		// console.log(regex.test(str));
	// expected output: true

	} );

});

document.addEventListener("DOMContentLoaded", function(event) {
	window.onstorage = function(e) {
		switch (e.key) {
			case "reload_receipt_list":
				table.ajax.reload();
				log2laravel('info',
					'reload_receipt_list :back to tab reloaded');

				localStorage.removeItem('reload_receipt_list')
				break;
		}
	}
});


function pump_search(search) {

	if (search.length > 4) {
		let pump_no = search.substring(4)

		tbl_url = "<?php echo e(route('ft.fuel.search.pump')); ?>";
		tbl_data = {
			'date': '<?php echo e($date); ?>',
			'pump_no': pump_no,
		}
		table.ajax.url( tbl_url ).load();
	} else {
		ft_tbl_url = "<?php echo e(route('ft-fuel-list-table')); ?>";
		ft_tbl_data = {
			'date': '<?php echo e($date); ?>',
		}
		table.ajax.url( ft_tbl_url ).load();
	}
}

function ptype_search(search) {
	if (search.length > 0) {
		let pump_no = search.substring(4)

		ft_tbl_url = "<?php echo e(route('ft.fuel.search.ptype')); ?>";
		ft_tbl_data = {
			'date': '<?php echo e($date); ?>',
			'ptype': search,
		}
		table.ajax.url( ft_tbl_url ).load();
	} else {
		tbl_url = "<?php echo e(route('ft-fuel-list-table')); ?>";
		tbl_data = {
			'date': '<?php echo e($date); ?>',
		}
		table.ajax.url( ft_tbl_url ).load();
	}
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/user/oceania/trunk/oceania/resources/views/fuel_fulltank/fuel_fulltank_receiptlist.blade.php ENDPATH**/ ?>