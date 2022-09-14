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
    text-decoration: none !important;
}

a {
    text-decoration: none !important;
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
    outline:  none !important;
}

button:hover{
     background: #19b83e !important;
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

<div id="landing-view" class="container-fluid">
    <div class="">
		<div class="clearfix"></div>
		<div class="d-flex mt-0 p-0 row" style="width:100%;margin-top:5px !important;margin-bottom:5px !important" >
			<div class="col-md-5 align-self-center" style="">
				<h2 style="margin-bottom: 0;">Location Product: Cost</h2>
			</div>

            <div class="col-md-1 align-self-center" style="">
				 <img src="/images/product/<?php echo e($prd_info->systemid); ?>/thumb/<?php echo e($prd_info->thumbnail_1); ?>"
					alt="Logo" width="70px" height="70px" alt="Logo"
					style="object-fit:contain;border-radius:10px;float:right;margin-left:0;margin-top:0;">
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
                <th valign="middle" class="text-left" style="">Date</th>

                <th valign="middle" class="text-center" style="">Cost</th>
                <th valign="middle" class="text-center" style="">Qty</th>

            </tr>
            </thead>
             <tbody id="shows">
                </tbody>

        </table>
        </div>
    </div>
</div>

<script>

var tableData = {
    systemid: "<?php echo e(request()->route('systemid')); ?>"
};

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
        { data: 'cost_date', name: 'cost_date' },
        { data: 'cost', name: 'cost' },
        { data: 'qty', name: 'qty' },
    ],
    // "order": [0, 'desc'],
    "columnDefs": [
        {"width": "30px", "targets": [0]},
        {"width": "100px", "targets": [2]},
        {"width": "100px", "targets": [3]},
        {"className": "dt-left vt_middle", "targets": [1]},
        {"className": "dt-center vt_middle nounderline", "targets": [2]},
        {"className": "dt-center vt_middle", "targets": [3]},
        // {"className": "dt-right vt_middle", "targets": [2]},
    ],
});

$(document).ready(function() {
    locProdCostTable.ajax.reload();
});

</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/user/oceania/trunk/oceania/resources/views/local_price/prodloc_cost.blade.php ENDPATH**/ ?>