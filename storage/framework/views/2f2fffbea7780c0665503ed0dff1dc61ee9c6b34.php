<?php $__env->startSection('styles'); ?>

<style>
.butns{
	display: none
}
th{
vertical-align: middle !important;
	text-align: center
}
td{
	vertical-align: middle !important;
}
.bg-primary:hover{
	color:white;
}
</style>

<div id="landing-view">
<!--white abalone-->
<style media="screen">
a:link{
	text-decoration: none!important;
}
@media (min-width: 1025px) {
	#ogProductLeger{
		table-layout: fixed;
	}
	.remarks {
		white-space: nowrap;
		overflow-x: hidden;
		text-overflow: ellipsis;
	}
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate{
    color: black !important;
    font-weight: normal !important;
}

#void_stamp{
	font-size:100px;
	color:red;
	position:absolute;
	z-index:2;
	font-weight:500;
	margin-top:130px;
	margin-left:15%;
	transform:rotate(45deg);
	display:none;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<div class="container-fluid">
<div class="row"
	style="width:100%;padding-top:0;height:70px;margin-top:5px;margin-bottom:5px">
	<div class="col-md-5" style="align-self:center">
		<h2 class="mb-0 pt-0">
			Location Product: Product Ledger
		</h2>
	</div>

	<div class="col-md-1" style="align-self:center">
	<?php if(!empty($product->thumbnail_1) && file_exists(public_path("/images/product/".$product->systemid."/thumb/".$product->thumbnail_1))): ?>

		<img src="/images/product/<?php echo e($product->systemid); ?>/thumb/<?php echo e($product->thumbnail_1); ?>"
			alt="Logo" width="70px" height="70px" alt="Logo"
			style="object-fit:contain;float:right;margin-left:0;margin-top:0;">
	<?php endif; ?>
	</div>

	<div class="col-md-4" style="align-self:center;float:left;padding-left:0">
		<h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;">
			<?php if($product->name??""): ?>
				<?php echo e($product->name??""); ?>

			<?php else: ?>
				Product Name <?php endif; ?>
		</h4>
		<p style="font-size:18px; margin-bottom:0"> <?php echo e($product->systemid??""); ?></p>
	</div>
	<div class="col-md-3" style="float: right;">
	</div>
	</div>

	<div class="table-responsive mb-5" style="overflow-x: hidden;">
	<table class="table table-bordered" id="ogProductLeger" style="width: 100%;">
	<thead class="thead-dark">
		<tr>
		<th class="text-center" style="width:30px;">No</th>
		<th class="text-center" style="width:200px;">Document&nbsp;No</th>
		<th class="text-center" style="width:150px">Type</th>
		<th class="text-center" style="width:120px">Last&nbsp;Update</th>
		<th class="text-left" style="width:auto;">Location</th>
		<th class="text-center" style="width:80px">Cost</th>
		<th class="text-center" style="width:80px">Qty</th>
		</tr>
	</thead>

	<!--
	Types of ledger data (table names):
	1. opos_receiptproduct
	2. stockreportproduct
	3. opos_refund
	 -->

	<tbody>
		<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr>
			<td style="text-align: center;"><?php echo e($loop->index + 1); ?></td>

			<?php if($row->doc_type == "Cash Sales"): ?>
			<td style="text-align: center;
				<?php if($row->status=='voided'): ?>
					background-color:red;color:white;font-weight:bold;
				<?php endif; ?>">
				<a href="#" style="text-decoration: none;"
					onclick="showReceipt(<?php echo e($row->id); ?>)"><?php echo e($row->systemid); ?>

				</a>
			  </td>
			  
			<?php elseif($row->doc_type == "Received"): ?>
			<td style="text-align: center;">
				<a href="javascript:window.open('<?php echo e(route('receiving_list_id',$row->systemid)); ?>')"
				style="text-decoration: none;"><?php echo e($row->systemid); ?>

				</a>
			</td>
			<?php else: ?>
			<td style="text-align: center;">
				<a href="javascript:window.open('<?php echo e(route('stocking.stock_report', $row->systemid)); ?>')"
				style="text-decoration: none;"><?php echo e($row->systemid); ?>

				</a>
			</td>
			<?php endif; ?>

			<td style="text-align: center;" nowrap>
				<?php if($row->doc_type == 'Stockin'): ?>
					Stock In
				<?php elseif($row->doc_type == 'Stockout'): ?>
					Stock Out
				<?php else: ?>
					<?php echo e(ucwords($row->doc_type)); ?>

				<?php endif; ?>
			</td>
			<td style="text-align: center;" nowrap>
				<?php if($row->status=='voided'): ?>
					<?php echo e(date('dMy H:i:s', strtotime($row->voided_at??''))); ?>

				<?php else: ?>
					<?php echo e(date('dMy H:i:s', strtotime($row->created_at??''))); ?>

				<?php endif; ?>
			</td>
			<td style="text-align: left" nowrap>
				<?php echo e($location->name); ?>

			</td>
			<td style="text-align: center;" nowrap>
			
				<?php if($row->doc_type == 'Stockout'): ?>
				<a href="javascript:void(0)"
					style="text-decoration: none;"
					onclick="show_cost_breakdown()">
						<?php echo e(number_format(($row->cost/100),2)); ?>

				</a>
				<?php else: ?>
					<?php echo e(number_format(($row->cost/100),2)); ?>

				<?php endif; ?>
				
			</td>
			<td style="text-align: center;">
				<?php if($row->status=='voided'): ?>
					0
				<?php else: ?>
					
					<?php echo e(($row->quantity)); ?>

				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</tbody>
	</table>
	</div>

	<div class="modal fade" id="eodModal_1" tabindex="-1" role="dialog"
	style="overflow:auto;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered mw-75"
		style="width:370px" role="document">
	<div id="recipt-model-div" class="modal-content bg-white"></div>
  </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<div id="productResponce"></div>
<div id="response"></div>

<div class="modal fade" id="normalCostModal"  tabindex="-1"
	role="dialog"  aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered modal-md  mw-75 w-50"
		role="document">
		<div class="modal-content modal-inside bg-purplelobster" >
			<div class="modal-header"> 
				<h4 id="qty_cost_tbl" style="margin-bottom:0">
					Document No: 1110000000XXX
				</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered align-content-center"
					id="qty_cost_tbl" style="width:100%">
					<thead class="thead-dark">
						<tr style="line-height:18px"> 
							<th>Cost</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody class="tablebody">
						<tr class="text-center" style="line-height:18px">
							<td style="background: white;">
								0.00
							</td>
							<td style="background: white;">
								2
							</td>
						</tr>
						<tr class="text-center" style="line-height:18px">
							<td style="background: white;text-align: center;">
								1.50
							</td>
							<td style="background: white;text-align: center;">
								3
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
function showReceipt(id){
	$('#eodSummaryListModal').modal('hide').html();
	$('#optlistModal').modal('hide').html();
	$('#receiptoposModal').modal('hide');
	$.ajax({
		url: "/local_cabinet/eodReceiptPopup/"+id,
		// headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: 'get',
		success: function (response) {
			// console.log(response);
			$('#recipt-model-div').html(response);
			$('#eodModal_1').modal('show');
		},
		error: function (e) {
			$('#responseeod').html(e);
			$("#msgModal").modal('show');
		}
	});
}


function show_cost_breakdown() {
	$('#normalCostModal').modal('show');
}

$(document).ready(function () {
	var tableinventory =  $('#ogProductLeger').DataTable();
});


</script>
</div>

<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/inv_stockmgmt/productledger.blade.php ENDPATH**/ ?>