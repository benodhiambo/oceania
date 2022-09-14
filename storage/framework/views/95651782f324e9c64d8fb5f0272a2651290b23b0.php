<div class="row">
	<div class="col-md-1 currencyall">
		<?php $__currentLoopData = $currencys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<p class="currencyid" id="<?php echo e($currency->id); ?>" style="cursor: pointer;
			<?php if(isset($company->currency_id)): ?>
				<?php if($currency->id == $company->currency_id): ?>
					font-weight: bold; color:#34dabb;
				<?php endif; ?>
			<?php endif; ?>"
			>
			<?php echo e($currency->code); ?>

		</p>
	</div>

	<div class="col-md-1 currencyall">
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php /**PATH /home/user/oceania/trunk/oceania/resources/views/screen_d/currency.blade.php ENDPATH**/ ?>