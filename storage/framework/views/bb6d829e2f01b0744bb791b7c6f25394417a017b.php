<tr style="color: white; height: 40px;padding-top: 20px;border-bottom: 2px #a0a0a0 solid;
	color:white;">
	<th  class="text-center" style="width: 65px;">
	Lot&nbsp;No
	</th>
	<th class="text-center" style="width: 140px;">
	Lot&nbsp;ID
	</th>
	<th class="text-center" style="width: 128px;">
	In
	</th>
	<th class="text-center" style="width: 128px;">
	Out
	</th>
	<th class="text-center" style="width: 110px;">
	Charger
	</th>
	<th class="text-center" style="width:100px;">
	<?php if( $current_setting_mode == 'hour' ): ?>
			Hour
		<?php else: ?>
			kWh
		<?php endif; ?>
	</th>
	<th class="text-center" style="width:100px;">
	Rate
	</th>
	<th class="text-center" style="width: 120px;">
	<?php echo e(empty($terminal->currency) ? 'MYR': $terminal->currency); ?>


	</th>
	<th class="text-center" style="width: 110px;">
	
	</th>
</tr>

<?php $__currentLoopData = $carparkOperas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opera): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
?>

<tr style="color: white; height: 40px;padding-top: 20px;">
	<td class="text-center" style="width: 65px;">
		<?php echo e($opera->lot_no); ?>

	</td>
	<td class="text-center" style="width: 140px;">
		<?php echo e($opera->systemid); ?>

	</td>

	<td class="text-center" style="width: 128px;">
		<?php if( !empty( $opera->carparkoper->in ) ): ?>
			<?php echo e($opera->carparkoper->in==null?'-':date("dMy h:i:s",
				strtotime($opera->carparkoper->in))); ?>

		<?php endif; ?>
	</td>
	<td class="text-center" style="width: 128px;">
		<?php if( !empty( $opera->carparkoper->out ) ): ?>
			<?php echo e($opera->carparkoper->out==null?'-':date("dMy h:i:s",
				strtotime($opera->carparkoper->out))); ?>

		<?php endif; ?>
	</td>
	<?php if(( isset( $opera->carparkoper->in ) && $opera->carparkoper->in!=null &&
		  isset( $opera->carparkoper->out ) && $opera->carparkoper->out!=null &&
		 $opera->carparkoper->payment==0)): ?>
		<td class="text-center" style="width: 110px;padding-top:10px;">
			<button class="btn-prawn-inactive btn
				trigger_active_<?php echo e($opera->carparkoper->id); ?>

				active_button  active_button_active "
				style="min-width: 75px; height: 37.53px; margin-bottom: 5%;cursor: text;pointer-events:none;">Active
			</button>
		</td>
		<?php else: ?>
		<td class="text-center" style="width: 110px;padding-top:10px;">
			<button onclick="statusClick(<?php echo e($opera->id); ?>,'<?php echo e($opera->amount); ?>',
				<?php echo e(@$opera->carparkoper->id); ?>)"
				class="btn
				<?php if( ( !empty( $opera->carparkoper->in ) &&
					   !isset($opera->carparkoper->out) ) ): ?> btn-prawn-custom
				<?php else: ?> btn-prawn-inactive
				<?php endif; ?>
				active_button active_button_active "
				style="min-width: 75px; height: 37.53px; margin-bottom: 5%">Active
			</button>
		</td>
		<?php endif; ?>

	<td class="text-center" style="width:100px;">

		
		<?php if( $current_setting_mode == 'hour' ): ?>
		<?php if( empty( $opera->carparkoper->in ) OR empty( $opera->carparkoper->out ) ): ?>
			0
		<?php else: ?>
			<?php echo e($opera->hours); ?>

		<?php endif; ?>
		<?php else: ?>
		<?php if($opera->stop_meter == 0 or $opera->stop_meter == null): ?>
		0.000

		<?php else: ?>
		<?php echo e(number_format(($opera->stop_meter - $opera->start_meter) / 1000,3)); ?>


		<?php endif; ?>
		<?php endif; ?>
	</td>

	<td class="text-center" style="width:100px;">
	<?php if( $current_setting_mode == 'hour' ): ?>
	<?php echo e(number_format($opera->rate / 100,2)); ?>


		<?php else: ?>
		<?php echo e(number_format($opera->kwh / 100,2)); ?>


		<?php endif; ?>
	</td>
	
	<td class="text-center" style="width: 120px;">
	<?php if( $current_setting_mode == 'hour' ): ?>
	<?php echo e(number_format(($opera->rate / 100) * $opera->hours,2)); ?>


		<?php else: ?>
		<?php if($opera->stop_meter == 0): ?>
		0.00

		<?php else: ?>
		<?php echo e(number_format((((($opera->stop_meter - $opera->start_meter) / 1000)*$opera->kwh) / 100),2)); ?>


		<?php endif; ?>
		<?php endif; ?>
	</td>
	<!-- <td class="text-center" style="width:5%;">
	<?php if( $opera->heartbeat != 'no' ): ?>
	<i class="fas fa-circle " style="color: green;"></i>
	<?php else: ?>
	<i class="fas fa-circle " style="color: grey;"></i>
	<?php endif; ?>
	</td> -->
	<td class="text-center" style="width: 110px;padding-top:10px;">
		<?php if((isset( $opera->carparkoper->in ) && $opera->carparkoper->in!=null &&
			isset( $opera->carparkoper->out ) && $opera->carparkoper->out!=null)): ?>
			<?php if($opera->carparkoper->payment!=0): ?>
			<div style="position:relative;left:30px">
				<button
					class="btn-pay-prawn-inactive btn
					trigger_save_<?php echo e($opera->systemid); ?> active_button"
					style="margin-left:10px;min-width: 75px;
					height: 37.53px; margin-bottom: 5%; cursor: text;pointer-events:none;">Pay
				</button>
			</div>
			<?php else: ?>
			<div style="position:relative;">
				<button onclick="payClick(
					<?php echo e($opera->systemid); ?>,
					<?php echo e($opera->hours); ?>,
					<?php echo e($opera->rate); ?>,
					'<?php echo e($opera->rate); ?>',
					'<?php echo e((($opera->stop_meter - $opera->start_meter) / 1000)*$opera->rate); ?>',
					<?php echo e((($opera->stop_meter - $opera->start_meter) / 1000)*$opera->rate); ?>,
					'<?php echo e($opera->carparkoper->id); ?>',
					'<?php echo e($opera->id); ?>',
					<?php echo e(($opera->stop_meter - $opera->start_meter) / 1000); ?>,
					'<?php echo e(($opera->stop_meter - $opera->start_meter) / 1000); ?>',
					'<?php echo e($current_setting_mode); ?>',
					'<?php echo e($opera->kwh); ?>'
					)"
					class="btn-pay-prawn-custom btn
					trigger_save_<?php echo e($opera->systemid); ?> active_button"
					style="min-width: 75px;
					height: 37.53px; margin-bottom: 5%">Pay
				</button>
			</div>
			<?php endif; ?>

		<?php else: ?>

		<div style="position:relative;">
		<button
			class="btn-pay-prawn-inactive btn
				trigger_save_<?php echo e($opera->systemid); ?> active_button"
			style="min-width: 75px;
				height: 37.53px; margin-bottom: 5%;cursor: text;pointer-events:none;">Pay
		</button>
		</div>

		<?php endif; ?>
	</td>
</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php if(isset($showmodal)): ?>
<script>
$(function() {
	$('#statusModalLabelMsg').html('No connection could be made');
	$('#modalMessage').modal('show');
});
</script>
<?php endif; ?>

<script>
	$(document).ready(function(){
		localStorage.setItem('stop_count_var', <?php echo e($stop_count); ?>);
		localStorage.setItem('transaction_count_var', <?php echo e($transaction_count); ?>);
		localStorage.setItem('paid_count_var', <?php echo e($paid_count); ?>);
		});
</script>
<?php /**PATH /home/user/oceania/trunk/oceania/resources/views/carpark/carparklot_table.blade.php ENDPATH**/ ?>