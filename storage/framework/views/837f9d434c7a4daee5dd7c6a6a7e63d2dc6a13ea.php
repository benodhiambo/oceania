<script src="<?php echo e(asset('js/number_format.js')); ?>"></script>
<!--Modal EodPrint-->
<script type="text/javascript">
/* calculate initial values on modal opening*/

$('#eodModal_1').on('show.bs.modal', function (e) {

    let cash = isNaN(parseInt('<?php echo e($cash); ?>')) ? 0 : parseInt('<?php echo e($cash); ?>')
    let non_op_cash_in = isNaN(parseInt('<?php echo e($non_op_cash_in); ?>')) ? 0 : parseInt('<?php echo e($non_op_cash_in); ?>')
    let non_op_cash_out = isNaN(parseInt('<?php echo e($non_op_cash_out); ?>')) ? 0 : parseInt('<?php echo e($non_op_cash_out); ?>')
    let sales_drop = isNaN(parseInt('<?php echo e($sales_drop); ?>')) ? 0 : parseInt('<?php echo e($sales_drop); ?>')
    let actual_drawer_amt = isNaN(parseInt('<?php echo e($actual_drawer_amount); ?>')) ? 0 : parseInt('<?php echo e($actual_drawer_amount); ?>')

    exp_amt = cash + non_op_cash_in - non_op_cash_out - sales_drop
    difference = actual_drawer_amt - exp_amt

    <?php if(empty($non_op_cash_in)): ?>
		$("#non_op_cash_in").attr("placeholder", "0.00");
	<?php else: ?>
        let noci = '<?php echo e($non_op_cash_in); ?>'
		$("#non_op_cash_in").val(number_format((noci) / 100, 2))
	<?php endif; ?>

    <?php if(empty($non_op_cash_out)): ?>
		$("#non_op_cash_out").attr("placeholder", "0.00");
	<?php else: ?>
        let noco = '<?php echo e($non_op_cash_out); ?>'
        $("#non_op_cash_out").val(number_format((noco) / 100, 2))
	<?php endif; ?>

    <?php if(empty($sales_drop)): ?>
		$("#sales_drop").attr("placeholder", "0.00");
	<?php else: ?>
        let sd = '<?php echo e($sales_drop); ?>'
        $("#sales_drop").val(number_format((sd) / 100, 2))
	<?php endif; ?>

    <?php if(empty($actual_drawer_amount)): ?>
		$("#actual_drawer_amount").attr("placeholder", "0.00");
	<?php else: ?>
        let ada = '<?php echo e($actual_drawer_amount); ?>'
        $("#actual_drawer_amount").val(number_format((ada) / 100, 2))
	<?php endif; ?>

    $("#expected_amount").text(number_format((exp_amt) / 100, 2))
    $("#difference").text(number_format((difference) / 100, 2))

	$(this).off('show.bs.modal');
})

    var shift_id = "<?php echo e($pshiftid); ?>"
    function preset_input(num, field){
        isType=1
        if(num==='0.0' || num==='0.00')
            isType=0

        var newnum = parseInt(num.replace(".", ""));
        txt=atm_money(newnum)
        $("#"+field).val(txt)
    }


      function atm_money(num) {
        if (num.toString().length == 1) {
          return '0.0' + num.toString();
        } else if (num.toString().length == 2) {
          return '0.' + num.toString();
        } else if (num.toString().length == 3) {
          return num.toString()[0] + '.' + num.toString()[1] +
            num.toString()[2];
        } else if (num.toString().length >= 4) {
          return num.toString().slice(0, (num.toString().length - 2)) +
            '.' + num.toString()[(num.toString().length - 2)] +
            num.toString()[(num.toString().length - 1)];
        }
      }

      function cash_in(num) {
        preset_input(num, 'non_op_cash_in')
        calculate_expected_amount()
      }
      function cash_out(num) {
        preset_input(num, 'non_op_cash_out')
        calculate_expected_amount()
      }
      function sales_drop(num) {
        preset_input(num, 'sales_drop')
        calculate_expected_amount()
      }
      function actual_drawer_amount(num) {
        preset_input(num, 'actual_drawer_amount')
        calculate_difference()
      }

      function calculate_expected_amount() {

          let rcash = isNaN(parseInt('<?php echo e($cash); ?>')) ? 0 : parseInt('<?php echo e($cash); ?>')
          let rnoci = $("#non_op_cash_in").val().replace(/,/g, '')
          let rnoco = $('#non_op_cash_out').val().replace(/,/g, '')
          let rsd = $('#sales_drop').val().replace(/,/g, '')

        let exp_amt = 0.00

        let cash = isNaN(rcash) ? 0 : rcash*1
        let noci = isNaN(rnoci) ? 0 : rnoci*100;
        let noco = isNaN(rnoco) ? 0 : rnoco*100;
        let sd = isNaN(rsd) ? 0 : rsd*100;

        exp_amt = cash + noci - noco - sd

        $('#expected_amount').text((exp_amt/100).toFixed(2))

        calculate_difference()
  }

  function calculate_difference() {

    let difference = 0.00

    // let actual_drawer_amt = isNaN(parseFloat($("#actual_drawer_amount").val())) ? parseFloat('0.00') : parseFloat($("#actual_drawer_amount").val());

    let rada = $("#actual_drawer_amount").val().replace(/,/g, '')
    let ada = isNaN(rada) ? 0 : rada*100;

    // let expected_amount = parseFloat($("#expected_amount").text());

    let rea = $("#expected_amount").text().replace(/,/g, '')
    let ea = isNaN(rea) ? 0 : rea*100;

    difference = ada - ea

    $("#difference").html(number_format((difference) / 100, 2))
    // $('#difference').html(difference.toFixed(2))

  }

</script>
<!--Modal Body Starts-->
<div class="modal-body" id="pss_modal" style="font-size: 14px;">
    <!--Section 1 starts-->
    <div class="row" style="text-align:center;">
        <div class="col-md-12 text-center  pr-5 pl-5" style="font-size: 15px">
            <strong>
                <?php echo e(!empty($company->name)?$company->name:"Ocosystem Ltd"); ?>

                (<?php echo e(!empty($company->business_reg_no)?$company->business_reg_no:"565565"); ?>

                ) <?php echo e(!empty($company->gst_vat_sst)?"(SST No. ".$company->gst_vat_sst.")":""); ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center  pr-5 pl-5" style="font-size: 10px">
            <strong style="font-size: 14px;">
                <?php echo e(!empty($company->office_address)?$company->office_address:"1, King Cross, Cheras, 56100 Kuala Lumpur, Malaysia"); ?></strong>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-7 pr-0">
            <strong>Personal Shift Summary</strong>
        </div>
        <div class="col-md-5 pl-1 text-right">
            <strong>
		  	<?php echo e(date("dMy H:i:s", strtotime($login_time??''))); ?> <?php echo e($logout_time); ?>

		   </strong>
        </div>
    </div>

    <hr style="margin-top:5px !important;
					margin-bottom:5px !important;
					border: 0.5px solid #a0a0a0;">

    <div class="row">
        <div class="col-md-6">

        </div>
        <div class="col-md-2">
            <strong class="global_currency"></strong>
        </div>
        <div class="col-md-4" style="text-align: right; font-size:17px">
            <strong
                id="item_amount"><b><?php echo e(empty($company->currency->code) ? 'MYR': $company->currency->code); ?></b></strong>
        </div>
    </div>

    <hr style="margin-top:5px !important;
					margin-bottom:5px !important;
					border: 0.5px solid #a0a0a0;">

    <div class="row">
        <div class="col-md-6">
            Today Sales
        </div>
        <div class="col-md-6" style="text-align: right;">
            <strong style="font-weight:normal" id="sales">
                <?php echo e($sales); ?></strong>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php echo e(!empty($terminal->taxtype)?strtoupper($terminal->taxtype):"SST"); ?> <?php echo e($terminal->tax_percent??"6"); ?>%
        </div>
        <div class="col-md-6" style="text-align: right;">
            <strong style="font-weight:normal" id="taxtype">
			   <?php echo e($tax); ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            Rounding
        </div>
        <div class="col-md-6" style="text-align: right;">
            <strong style="font-weight:normal" id="rounding">
                <?php echo e($round); ?>

            </strong>
        </div>
    </div>
    <!--section 1 ends-->
    <hr style="margin-top:5px !important;
					margin-bottom:5px !important;
					border: 0.5px solid #a0a0a0;">

    <!--section 2 starts-->
    <div class="row">
        <div style="font-weight:normal" class="col-md-6">
            Cash
        </div>
        <div class="col-md-6" style="text-align: right;">
            <strong style="font-weight:normal" id="cash">
				<?php echo e(number_format(($cash/100),2)); ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            Credit Card
        </div>
        <div class="col-md-6" style="text-align: right;">
            <strong style="font-weight:normal" id="creditcard">
                <?php echo e($creditcard); ?></strong>
        </div>
    </div>

	<div class="row">
        <div class="col-md-8">
			Wallet
        </div>
        <div class="col-md-4" style="text-align: right;">
            <strong style="font-weight:normal" id="wallet">
				<?php echo e($wallet); ?>

			</strong>
        </div>
    </div>

	<div class="row">
        <div class="col-md-8">
			Credit Account
        </div>
        <div class="col-md-4" style="text-align: right;">
            <strong style="font-weight:normal" id="creditac">
			<?php echo e($creditac); ?>

			</strong>
        </div>
    </div>

    <?php if($terminal_btype->btype??"" == 'petrol_station'): ?>
        <div class="row">
            <div class="col-md-6">
                Trade Debtor
            </div>
            <div class="col-md-2">
                <strong
                    class="global_currency">
					<?php echo e(empty($company->currency->code) ? 'MYR': $company->currency->code); ?>

				</strong>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <strong id="tradedebtor">0.00</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                Cheque
            </div>
            <div class="col-md-2">
                <strong
                    class="global_currency">
					<?php echo e(empty($company->currency->code) ? 'MYR': $company->currency->code); ?>

				</strong>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <strong id="cheque">0.00</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                Manual OPT
            </div>
            <div class="col-md-2">
                <strong
                    class="global_currency">
					<?php echo e(empty($company->currency->code) ? 'MYR': $company->currency->code); ?>

				</strong>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <strong id="manualopt"><?php echo e(number_format((@$OPT/100),2)); ?></strong>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                Fleet Card
            </div>
            <div class="col-md-2">
                <strong
                    class="global_currency"><?php echo e(empty($company->currency->code) ? 'MYR': $company->currency->code); ?>

				</strong>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <strong id="fleetcard">0.00</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                Cash Card
            </div>
            <div class="col-md-2">
                <strong
                    class="global_currency">
					<?php echo e(($company->currency->code) ? 'MYR': $company->currency->code); ?>

				</strong>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <strong id="cashcard">0.00</strong>
            </div>
        </div>
	<?php endif; ?>

	<!--section 2 ends-->
    <hr style="margin-top:5px !important;
		margin-bottom:5px !important;
		border: 0.5px solid #a0a0a0;">

    <!--section 3 starts-->
    <div class="row">
        <div class="col-md-8 text-left">
            <strong style="font-weight:normal">Non-Operational Cash In</strong>
        </div>
        <div class="col-md-4 text-right">
            <input type="text" id="non_op_cash_in"
				oninput="cash_in(this.value)"
                maxlength="9"
				style="width:100%;padding: 1px 1px; line-height: 0px; text-align: right;border-width:1px;padding-right:3px;margin-left:3px;border-radius:4px">
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 text-left">
            <strong style="font-weight:normal">(-)Non-Operational Cash Out</strong>
        </div>
        <div class="col-md-4 text-right">
            <input type="text" id="non_op_cash_out"
				oninput="cash_out(this.value)" size="10"
                maxlength="9"
				style="width:100%;margin-top: 1px;margin-bottom: 1px;padding: 1px 1px; line-height: 0px; text-align: right;border-width:1px;padding-right:3px;margin-left:3px;border-radius:4px">
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 text-left">
            <strong style="font-weight:normal">(-)Sales Drop</strong>
        </div>
        <div class="col-md-4 text-right">
            <input type="text" id="sales_drop" placeholder="0.00"
				oninput="sales_drop(this.value)" size="10"
                maxlength="9"
				style="width:100%;padding: 1px 1px; line-height: 0px; text-align: right;border-width:1px;padding-right:3px;margin-left:3px;border-radius:4px">
        </div>
    </div>
    <hr style="margin-top:5px !important;
    margin-bottom:5px !important;
    border: 0.5px solid #a0a0a0;">
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Expected Amount</strong>
        </div>
        <div id="expected_amount" class="col-md-6 text-right">
          0.00
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 text-left">
            <strong style="font-weight:normal">Actual Drawer Amount</strong>
        </div>
        <div class="col-md-4 text-right">
            <input type="text" id="actual_drawer_amount"
				oninput="actual_drawer_amount(this.value)" size="10"
                maxlength="9"
				style="width:100%;padding: 1px 1px; line-height: 0px; text-align: right;border-width:1px;margin-left:3px;padding-right:3px;border-radius:4px">
        </div>
    </div>
    <hr style="margin-top:5px !important;
    margin-bottom:5px !important;
    border: 0.5px solid #a0a0a0;">
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Difference</strong>
        </div>
        <div id="difference" class="col-md-6 text-right">
            0.00
        </div>
    </div>
    <hr style="margin-top:5px !important;
    margin-bottom:5px !important;
    border: 0.5px solid #a0a0a0;">
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">C-Store Total</strong>
        </div>
        <div class="col-md-6 text-right">
            <?php echo e(!empty($cstore_total) ? number_format(($cstore_total/100), 2) : '0.00'); ?>

        </div>
    </div>
    <?php if(!empty($fuel_products)): ?>
        <?php $__currentLoopData = $fuel_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="row">
            <div class="col-md-6 text-left">
                <strong style="font-weight:normal"><?php echo e($product->name); ?></strong>
            </div>
            <div class="col-md-6 text-right">
                <?php echo e(number_format(($product->sales/100), 2)); ?>

            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!--section 3 ends-->
    <hr style="margin-top:5px !important;
    margin-bottom:5px !important;
    border: 0.5px solid #a0a0a0;">

    <!--section 4 starts-->
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Location</strong>
        </div>
        <div class="col-md-6 text-right">
            <?php echo e($location->name??""); ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Location ID</strong>
        </div>
        <div class="col-md-6 text-right">
            <?php echo e($location->systemid??""); ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Terminal ID</strong>
        </div>
        <div class="col-md-6 text-right">
            <?php echo e($terminal->systemid??""); ?>

        </div>
    </div>

    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Staff Name</strong>
        </div>
        <div class="col-md-6 text-right">
            <?php echo e($user->fullname??''); ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-left">
            <strong style="font-weight:normal">Staff ID</strong>
        </div>
        <div class="col-md-6 text-right">
            <?php echo e($user->systemid??''); ?>

        </div>
    </div>

    <div class="row align-items-center mt-3">
        <div class="col-md-6">
    
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-success bg-receipt-print"
                    id="print_eod"
                    style="font-size:13px;"
                    onclick="print_pss(<?php echo e($pshiftid); ?>)">
                <strong>Print</strong>
            </button>
        </div>
    </div>
    <div class="row float-right"
         style="font-size:10px;padding-right:15px;margin-top:6px">
        <strong>Betta Forecourt v1.0<strong>
    </div>
    <!--section 4 ends-->
</div>
<!--Modal Body ends-->

<script type="text/javascript">

    /*
    $('#print_eod').attr('disabled', 'on');
	 */
    function print_pss(pshiftid) {
        $.ajax({
            url: "<?php echo e(route('local_cabinet.pss.print')); ?>",
            type: 'post',
            data: {
				'pshiftid':'<?php echo e($pshiftid); ?>',
				'login_time':'<?php echo e(request()->login_time); ?>',
				'user_systemid': '<?php echo e(request()->user_systemid); ?>',
				<?php if(!empty(request()->logout_time)): ?>
					'logout_time':'<?php echo e(request()->logout_time); ?>'
				<?php endif; ?>
		},
		success: function (response) {
			console.log("Printing PSS")
			var error1=false, error2=false;
			console.log('PR '+JSON.stringify(response));

			try {
				eval(response);
				console.log('eval working');
			} catch (exc) {
				error1 = true;
				console.error('ERROR eval(): '+exc);
			}

			if (!error1) { try {
					escpos_print_template();
					console.log('template working');
				} catch (exc) {
					error2 = true;
					console.error('ERROR escpos_print_template(): '+exc);
				}
			}

		 },
		error: function (e) {
			$('#response').html(e);
		}
	});
}

</script>
<?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/local_cabinet/pss.blade.php ENDPATH**/ ?>