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
    padding: 5px 9px;
}
.text-center.dt-right.vt_middle.center-text.sorting_desc,
.text-center.center-text.dt-right.vt_middle.sorting {
    text-align: center!important;
}

.dt-center.vt_middle.small-pad-y {
    padding-top: 0%;
    padding-bottom: 0%;
}
table ,tr,td{
    padding:0.29% !important;
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
			style="width:100%;margin-bottom: 5px; margin-top:5px; height:70px;">
			<div class="col-md-4 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Receiving Note</h2>
			</div>
             <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0; font-size:18px; font-weight:bold;"> <?php echo e($location->name); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($location->systemid); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> Doc No. <?php echo e($docId); ?></p>
			</div>
            <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($user->fullname); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($user->systemid); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($time); ?></p>
			</div>

			<div class="row col-md-2 text-left m-0 pr-0">
				<?php if($invoice_no != 0): ?>
				<div class="col-md-12 align-items-center d-flex pr-0" style="justify-content:flex-end">
                    <strong style="font-size:18px;">
		                <label class="mb-0">Invoice No:&nbsp;</label>
		                <span>
		                	<?php echo e($invoice_no); ?>

		                </span>
                    </strong>
                </div>
				<?php endif; ?>
                
                
                </div>

			</div>
		</div>
         <div style="margin-top: 0;margin-right:15px; margin-left:15px;">
            <table border="0" cellpadding="0" cellspacing="0" class="table " id="rec_note_table" style="margin-top: 0px; width:100%">
                <thead class="thead-dark"  >
		            <tr id="table-th" style="border-style: none">
		                <th valign="middle" class="text-center" style="width:30px">No</th>
		                <th valign="middle" class="text-center" style="width:180px;">Product ID</th>
		                <th valign="middle" class="text-left" style="">Product Name</th>
		                <th valign="middle" class="text-center" style="text-align:center !important;width:80px;">Price</th>
		                <th valign="middle" class="text-center no-pad" style="text-align:center !important;width:80px;">Qty</th>
		                <th valign="middle" class="text-center" style="text-align:center !important;width:80px;">Cost</th>
		                <th valign="middle" class="text-center" style="text-align:center !important;width:80px;">Cost Value</th>
		            </tr>
                </thead>
                <tbody id="shows">
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
var tableData = {
	systemid: "<?php echo e(Request::route('id')); ?>"
};

var rec_note_table = $('#rec_note_table').DataTable({
    "processing": false,
    "serverSide": true,
    "autoWidth": false,
    "language": {

            "zeroRecords": "No data available in table",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoFiltered": ""
        },
    "ajax": {
        /* This is just a sample route */
        "url": "<?php echo e(route('receiving_notes.confirmed_datatable')); ?>",
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
    ]
});



$(document).ready(function() {
    rec_note_table.ajax.reload();
});

</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/receiving_note/cstore_receiving_note_confirmed.blade.php ENDPATH**/ ?>