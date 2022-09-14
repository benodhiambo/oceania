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
button> 1:focus{
        outline:  none !important;
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

.slim-cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

.pd_column {
	padding-top: 10px;
}

tr {
	height: 40px
}

.num_td{
	text-align: left;
}
.value-button {
	display: inline-block;
	font-size: 24px;
	line-height: 21px;
	text-align: center;
	vertical-align: middle;
	background: #fff;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	text-align: center;
	text-align: center;
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
.value-button {
	cursor:pointer;
}


.SearchBar input {
     height:10%;
     margin-top: 10px;
     background-color: #c9cec9;
     text-align: center;
     font-size: 18px;
     font-weight: bolder;
     color: #000;
     border-radius: 3%;
}
#buttons{
     position: fixed;
       left: 0;
  bottom: 0;
  width: 100%;

  color: white;
  text-align: center;
}
.bnt-primary{
    border-radius: 10px !important;
    background-color: rgb(17, 17, 253);
    color: #fff;
    padding: 3px;
}
</style>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common/mobile/mob_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('content'); ?>

<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		
         
            
            
         
         <div class="form-outline SearchBar">
             <input type="search" id="form1" class="form-control" placeholder="Search" aria-label="Search" />
        </div>
         <?php $__currentLoopData = $audited_report_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <div class="row d-flex justify-content-between p-4 text-bolder">
          <div class="col-8">
            <img src="/images/product/thumb/<?php echo e($product->thumbnail_1); ?>" alt="imf" style="height:25px;width:25px;">
              <strong><?php echo e($product->name); ?></strong>
          </div>
          <div class="col-2">
            <strong>  <?php echo e(number_format($product->Iqty)); ?></strong>
          </div>

          <div class="col-2">
            <button type="button" class="btn btn-primary mr-0 confirm-button"> - </button>
          </div>
         </div>
         <hr class="solid">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         <div class="row d-flex justify-content-between p-3 ml-2 mr-3" id="buttons">
            <button type="button" class="btn btn-success sellerbutton mr-0 bg-confirm-button"
            style="height:70px;width:187px !important; border-radius:10px !important;"id="fulltank-rl" >
             <span style="font-size:21px !important; font-weight:bolder;">
            Confirm
             </span>
        </button>
            <button type="button" class="btn btn-success "style="height:70px;width: 187px; border-radius:10px !important;"
					        id="fulltank-rl">
                     <span style="font-size:21px !important; font-weight:bolder;">

                            Scan
                     </span>
            </button>
         </div>

	</div>

<?php $__env->startSection('script'); ?>
<script>
$(document).ready(function() {
    $('#eodsummarylistd').dataTable({
        "aLengthMenu": [[10, 50, 75, -1], [10, 25, 50, 100]],
        "iDisplayLength": 10,
        'aoColumnDefs': [{
        'bSortable': false,
        'aTargets': ['nosort']
    }]
    });
    let dataLength = "<?php echo e(count($audited_report_list) +1); ?>"

    for (let i = 1; i < dataLength ; i++) {
        var num_element = document.getElementById('number_'+i);
        var diff = document.getElementById('diff_'+i);
        var qty = document.getElementById('qty_'+i);
		var value = parseFloat(num_element.value);

        diff.textContent = num_element.value - qty.textContent;
        // console.log(diff.textContent)
    }
} );

	$.ajaxSetup({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
	});

	var location_id = "<?php echo e($location->id); ?>";
	var tablestockin ={};
	var isConfirmEnabled = 0;

	var tableData = {};


	function increaseValue(id) {

		var num_element = document.getElementById('number_'+id);
        var diff = document.getElementById('diff_'+id);
        var qty = document.getElementById('qty_'+id);
		var value = parseFloat(num_element.value);

		value = isNaN(value) ? 0 : value;
		value++;
		num_element.value = value;

        diff.textContent = value - qty.textContent;

	}


	function decreaseValue(id) {
		var num_element = document.getElementById('number_'+id);
        var diff = document.getElementById('diff_'+id);
        var qty = document.getElementById('qty_'+id);

		var value = parseFloat(num_element.value);

			value = isNaN(value) ? 0 : value;
			value < 1 ? value = 1 : '';
			value--;
		num_element.value = value;
       return diff.textContent = value - qty.textContent;
	}

	function changeValueOnBlur(id) {
		x = 0;
		ele = document.querySelectorAll('.number');
		ele.forEach( (e) => x += parseInt(e.value));
        isConfirmEnabled = x;

        var num_element = document.getElementById('number_'+id)
        var diff = document.getElementById('diff_'+id);
        var qty = document.getElementById('qty_'+id);

       return diff.textContent = parseFloat(num_element.value) - qty.textContent;
	}

	setInterval(() => {
		if (isConfirmEnabled > 0) {
			$("#confirm_update").removeAttr('disabled');
			$("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
			$("#confirm_update").css('cursor','pointer');
		} else {
			$("#confirm_update").attr('disabled', true);
			$("#confirm_update").css('background','gray');
			$("#confirm_update").css('cursor','not-allowed');
		}
	}, 1500);

	function update_quantity() {
        const btn = document.getElementById('confirm');

        btn.addEventListener('click', function onClick() {
        btn.style.backgroundColor = '#A8A8A8';
        btn.style.color = 'white';
        });
        let dataLength = "<?php echo e(count($audited_report_list) +1); ?>"
        console.log(parseInt(dataLength));
        let container = [];
        let product = {}

        for(let i=1; i < parseInt(dataLength); i++) {
             let id = document.getElementById("product_"+i);
             let qty = document.getElementById('qty_'+i);
             var num_element = document.getElementById('number_'+i);
              var diff = document.getElementById('diff_'+i);

             product.product_id = id.textContent;
             product.qty = qty.textContent;
             product.audited_qty = parseFloat(num_element.value);
             product.difference = diff.textContent;
             container.push(product);
             product ={};

        }
      console.log(container)
		if (container.length < 1)
			return;

		$.ajax({
		  url: "<?php echo e(route('update_audited_notes')); ?>",
		  type: "POST",
		  'headers': {
				'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
			},
		  data: {container},
		  cache: false,
             success: function(dataResult){
			  //$("#productResponse").html(dataResult);
			messageModal(`Audited note created successfully`);
			tablestockin.ajax.reload();
		  }
		});
	}
</script>
<?php $__env->stopSection(); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/user/oceania/trunk/oceania/resources/views/cstore_audited_rpt/mobile_audited_note.blade.php ENDPATH**/ ?>