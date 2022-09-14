<!-- START OPOS Prepaid -->

<script>
/* Block to update Fuel, Filled, Litre and Price upon reload */
$(document).ready(function() {
	for (var i = 1; i <= {{ env('MAX_PUMPS') }}; i++) {
		var my_pump = i;
		var pump_has_been_selected = false;
		var pump_receipt = localStorage.getItem("pump_receipt_info");
		var deliveredPump = localStorage.getItem("pump_receipt_data_" + my_pump);

		if (pump_receipt != undefined &&
			pump_receipt != '' &&
			pump_has_been_selected === false) {

			if (pump_receipt == my_pump) {
				//pump_selected(my_pump, false, false);
				pump_has_been_selected = true;
			}
		}

		if (deliveredPump != undefined && deliveredPump != '') {
			pump_receipt = my_pump;

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: deliveredPump=' + deliveredPump);
		}

		if (deliveredPump != undefined &&
			deliveredPump != '' &&
			pump_receipt != '' &&
			pump_receipt != undefined &&
			pump_receipt > 0) {

			deliveredPump = JSON.parse(deliveredPump);

			var product_price = deliveredPump.price;

			// FP Fuel is being updated here
			localStorage.setItem('pump_data_fuel_' + my_pump, deliveredPump.dose)
			var pdata = localStorage.getItem('pump_data_fuel_' + my_pump);
			$('#total-fuel-pump-' + my_pump).text(pdata);

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: total-fuel-pump-' +
				my_pump + '=' + $('#total-fuel-pump-' + my_pump).text());

			var last_amt = deliveredPump.amount;

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: last_amt=' + last_amt);

			var final_litre = parseFloat(last_amt) / parseFloat(deliveredPump.price);

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: final_litre=' + final_litre.toFixed(2));

			if (final_litre == 'NaN') {
				final_litre = 0;
			}

			if (last_amt == undefined) {
				last_amt = 0.00;
			}

			$('#fuel-product-price-' + my_pump).text(product_price.toFixed(2));

			log2laravel('info', my_pump +
				': ***** BG1 prepaid: fuel-product-price-' +
				my_pump + '=' + $('#fuel-product-price-' + my_pump).text());


			var isNozzleDown = localStorage.getItem("isNozzleDown" + my_pump);

			if (isNozzleDown != undefined && isNozzleDown === 'yes') {
				$('#total-final-litre-' + my_pump).text(final_litre.toFixed(2));
				$('#total-final-filled-' + my_pump).text(last_amt);

				localStorage.setItem("pump_reset_data_" + my_pump, 'yes');
			}

			$('#payment-status-' + my_pump).text('Paid');
		}
	}
});


/* OBSOLETED
function process_enter(){
	log2laravel('info', '***** process_enter() *****');

	pump_no = selected_pump;

	cal_item_amount = $(`#item-amount-calculated-${pump_no}`).text();
	cal_sst 		= $(`#sst-val-calculated-${pump_no}`).text();
	cal_rounding 	= $(`#rounding-val-calculated-${pump_no}`).text();
	cal_total		= $(`#table-MYR-${pump_no}`).text();
	cal_change		= $(`change-val-calculated-${pump_no}`).text();

	product_id		= pumpData['pump' + pump_no].product_id;
	product_name	= $(`#table-PRODUCT-${pump_no}`).text();
	product_qty		= $(`#table-QTY-${pump_no}`).text();
	product_price	= $(`#table-PRICE-${pump_no}`).text();
	product_amount	= $(`#table-MYR-${pump_no}`).text();

	payment_type = dis_cash['pump'+pump_no].payment_type;

	if (dis_cash['pump'+selected_pump].payment_type == "cash"){
		cash_received =  (parseFloat(dis_cash['pump'+selected_pump].dis_cash)/100).toFixed(2);
	} else {
		cash_received = cal_total;
	}

	auth_id = pumpData['pump' + selected_pump].auth_id;

	$.post("{{ route('local_cabinet.receipt.create') }}", {
			cash_received,
			payment_type,
			cal_item_amount,
			cal_sst,
			cal_rounding,
			cal_total,
			product_id,
			product_name,
			product_qty,
			product_amount,
			product_price,
			pump_no,
			auth_id
		})
		.done( (response) => {

			console.log('PR local_cabinet.receipt.create:');
			console.log('PR ***** SUCCESS *****');
			console.log('response='+JSON.stringify(response));
			//my ESCPOS printing function
			receipt_id = response;
			//console.log('data='+JSON.stringify(data));

			// Save receipt_id in pumpData[]
			pumpData['pump'+selected_pump].receipt_id = receipt_id;

			// Need to have Qz.io running, otherwise print_receipt()
			// will bomb out and will not execute lines after it. We
			// trap the error so that we can still run even if Qz is
			// NOT running!!
			try {
				// Output receipt via thermal printer
				print_receipt(response);

				// Open cash drawer
				open_cashdrawer();

			} catch (error) {
				// This will catch if Qz.io is not being run!!
				//alert('ERROR! print_receipt(). Check Qz!!');
				//alert("ERROR print_receipt(): " + JSON.stringify(error));
				console.error("ERROR! Check if Qz.io is being run!!");
				console.error("ERROR: "+ JSON.stringify(error));
			}
		})
		.fail( (e) => console.error(e));

	if (pumpData['pump'+selected_pump].product)
		v3_pump_auth(selected_pump, pumpData['pump'+selected_pump].product_id);
}
*/
</script>
<!-- END OPOS Prepaid -->
