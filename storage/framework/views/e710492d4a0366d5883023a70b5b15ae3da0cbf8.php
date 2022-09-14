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
	color: black;
}

th, td {
	vertical-align: middle !important;
	text-align: center
}

label, .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: #000 !important;
}

.active_button {
	color: #ccc;
	border: 1px #ccc solid;
}
td{
    border:1px solid rgb(226, 223, 223) !important;
}

.active_button:hover,
.active_button:active,
.active_button_activated {
	background: transparent !important;
	color: #34dabb !important;
	border: 1px #34dabb solid !important;
	font-weight: bold;
}

.active_button_activated {
	background: transparent;
	color: #34dabb;
	border: 1px #34dabb solid;
	font-weight: bold;
}

.slim-cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

.typewriter {
    text-align: right;
    border-radius: 3px;
    overflow: hidden;
    white-space: nowrap;
    margin: 0 auto;
    letter-spacing: .0em;
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
ion-icon{
    cursor:pointer;
}
a{
    text-decoration: none !important;
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
		style="width:100%;margin-bottom: 5px; margin-top:5px">
		<div class="col-md-4 pl-0 align-self-center" style="">
			<h2 style="margin-bottom: 0;"> Audited Report</h2>
		</div>
		<div class="col-md-3 pl-0 align-self-center" style="">
			<h5 style="margin-bottom: 0;"> <?php echo e($location->name); ?></h5>
			<h5 style="margin-bottom: 0;"> <?php echo e($location->systemid); ?></h5>

		</div>
		<div class="col-md-3 pl-0 align-self-center" style="">
			<h5 style="margin-bottom: 0;"> <?php echo e($user->fullname); ?></h5>
			<h5 style="margin-bottom: 0;"> <?php echo e($user->systemid); ?></h5>

		</div>

			<div class="col-md-2 d-flex pr-0"
				style="justify-content:flex-end">
				<button class="btn btn-success sellerbutton mr-0 btn-sq-lg bg-confirm-button"
				onclick="update_quantity()" id="confirm_update"
				style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Confirm
			</button>


		</div>
	</div>
	<div class="mb-3">
		<table border="0" cellpadding="0" cellspacing="0" class="table "
			id="auditedReportList" style="margin-top: 0px; width:100%">
			<thead class="thead-dark"  >
                <tr id="table-th" style="border-style: none">
                    <th valign="middle" class="text-center" style="width:30px">No</th>
                    <th valign="middle" class="text-center" style="width:7%;">Product ID</th>
                    <th valign="middle" class="text-left" style="">Product Name</th>

                    <th valign="middle" class="text-center" style="width:7%;">Qty</th>
                    <th valign="middle" class="text-center" style="width:11.8%;">Audited Qty</th>
                    <th valign="middle" class="text-center" style="width:7%;">Difference</th>
                </tr>
            </thead>
             <tbody id="show">
             </tbody>
		</table>
	</div>
</div>
</div>

<?php $__env->startSection('script'); ?>

<script>
    var tablereport ={};

	var isConfirmEnabled = 0;
    var qtyincr = 0;
     detect_button();
     var container = [];
     var stockin_updates_openitemprod = [];
     var stockin_updates_locationprod = [];
    var stockout_updates_openitemprod = [];
    var stockout_updates_locationprod = [];

	var tableData = {};
	var tablereport= $('#auditedReportList').DataTable({
		"processing": false,
		"serverSide": true,
		"autoWidth": false,
        "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		"ajax": {
			/* This is just a sample route */
			"url": "<?php echo e(route('cstore.listAuditedRpt')); ?>",
			"type": "POST",
			data: function (d) {
                console.log(tableData)
				return $.extend(d, tableData);
			},
			'headers': {
				'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
			},
		},
		columns: [
			{data: 'DT_RowIndex'},
			{data: 'product_systemid', name: 'product_systemid'},
			{data: 'product_name', name: 'product_name'},
			{data: 'product_qty', name: 'product_qty'},
			{data: 'audited_qty', name: 'audited_qty'
            },
            {data: 'difference', name: 'difference'

            },
		],
		"order": [0, 'desc'],
		"columnDefs": [
			{"width": "30px", "targets": [0]},
			{"width": "160px", "targets": [1]},
			{"width": "60px", "targets": [3]},
			{"width": "100px", "targets": [4]},
			{
				"targets": 2, // your case first column
				"className": "text-left",
			},
			{"className": "dt-left vt_middle", "targets": [2]},
			{"className": "dt-right vt_middle slim-cell", "targets": [4]},
			{"className": "dt-center vt_middle slim-cell", "targets": [0, 1, 3]},
			{"className": "vt_middle slim-cell", "targets": [2]},
			//{"className": "vt_middle slim-cell", "targets": [6]},
			{orderable: false, targets: [-1]},
		],

	});

function increaseValue(id) {
	var num_element = document.getElementById('number_'+id);
	var value = parseFloat(num_element.value);
	value = isNaN(value) ? 0 : value;
	value++;
	num_element.value = value;
    let qty = $('#qty_'+id).text()
    let div = $('#diff_'+id);

    let datainfo = $('#qty_'+id).data('field');

    div.text(num_element.value - qty)

	qtyincr++;
    detect_button()
      if(parseInt(div.text()) > 0 ) {
            // Gat values/objects that do not include current product
            if(datainfo =='inventory'){
                stockin_updates_locationprod = stockin_updates_locationprod.filter(function(item){
                    if(!(item.product_id == id)) {
                            return item;
                        }
                    })

                // Add current product to the array
                stockin_updates_locationprod.push({
                    product_id: id,
                    qty:  parseInt(div.text())
                })
                console.log("locprd -in ==>"+ JSON.stringify(stockin_updates_locationprod))
            } else if(datainfo =='openitem'){
                 stockin_updates_openitemprod = stockin_updates_openitemprod.filter(function(me) {
                if(!(me.product_id == id)) {
                        return me;
                    }
                });

                 stockin_updates_openitemprod.push({
                    'product_id': id,
                    'qty': parseInt(div.text())
                });
                 console.log("openitem -in ==>"+JSON.stringify(stockin_updates_openitemprod))
            }
        }
       if(parseInt(div.text()) < 0 ) {

            // Gat values/objects that do not include current product
            if(datainfo =='inventory'){
                stockout_updates_locationprod = stockout_updates_locationprod.filter(function(item){
                    if(!(item.product_id == id)) {
                            return item;
                        }
                    })

                // Add current product to the array
                stockout_updates_locationprod.push({
                    product_id: id,
                    qty:  Math.abs(div.text())
                })
                console.log("locprd -out ==>"+ JSON.stringify(stockout_updates_locationprod))
            } else if(datainfo =='openitem') {
                 stockout_updates_openitemprod = stockout_updates_openitemprod.filter(function(me) {
                if(!(me.product_id == id)) {
                        return me;
                    }
                });

                 stockout_updates_openitemprod.push({
                    'product_id': id,
                    'qty': Math.abs(div.text())
                });
                 console.log("openitem -out ==>"+JSON.stringify(stockout_updates_openitemprod))
            }
        }
    container = container.filter(function(me) {
        if(me.product_id != id) {
            return me;
        }
    });
    container.push({
        'product_id':id,
        'audited':parseInt(num_element.value),
        'diff':parseInt(div.text()),
        'qty':parseInt(qty)
    })
     tableData ={
        'container':container
    }

}


function decreaseValue(id) {
	var num_element = document.getElementById('number_'+id);
	var value = parseFloat(num_element.value);
		value = isNaN(value) ? 0 : value;
		value < 1 ? value = 1 : '';
		value--;
	num_element.value = value;
    let qty = $('#qty_'+id).text()
    $('#diff_'+id).html(num_element.value - qty)
	qtyincr--;
    let datainfo = $('#qty_'+id).data('field');

    detect_button()
    if(parseInt(div.text()) > 0 ) {

        // Gat values/objects that do not include current product
        if(datainfo =='inventory'){
            stockin_updates_locationprod = stockin_updates_locationprod.filter(function(item){
                if(!(item.product_id == id)) {
                        return item;
                    }
                })

            // Add current product to the array
            stockin_updates_locationprod.push({
                product_systemid: id,
                qty:  parseInt(div.text())
            })
            stockin_updates_locationprod = locationprod
        } else if(datainfo =='openitem') {
                stockin_updates_openitemprod = stockin_updates_openitemprod.filter(function(me) {
            if(!(me.product_id == id)) {
                    return me;
                }
            });

                stockin_updates_openitemprod.push({
                'product_id': id,
                'qty': parseInt(div.text())
            });
        }
    }
    if(parseInt(div.text()) < 0 ) {

        // Gat values/objects that do not include current product
        if(datainfo =='locationprod'){
            stockout_updates_locationprod = stockout_updates_locationprod.filter(function(item){
                if(!(item.product_id == id)) {
                        return item;
                    }
                })

            // Add current product to the array
            stockout_updates_locationprod.push({
                product_systemid: id,
                qty:  Math.abs(div.text())
            })

        } else if(datainfo =='openitemprod') {
                stockout_updates_openitemprod = stockout_updates_openitemprod.filter(function(me) {
            if(!(me.product_id == id)) {
                    return me;
                }
            });

                stockout_updates_openitemprod.push({
                'product_id': id,
                'qty':Math.abs(div.text())
            });
        }
    }
    container = container.filter(function(me) {
            if(me.product_id != id) {
                return me;
            }
        });
    container.push({
        'product_id':id,
        'audited':parseInt(num_element.value),
        'diff':parseInt(div.text()),
        'qty':parseInt(qty)
    })


    tableData ={
        'container':container
    }

}

    function changeValueOnBlur(id) {
        x = 0;
        ele = document.querySelectorAll('.number');
        ele.forEach( (e) => x += parseInt(e.value));
        isConfirmEnabled = x;

         num_element = $('#number_'+id).val();
         diff = $('#diff_'+id);
         qty = $('#qty_'+id).text();

        diff.text() = num_element - qty;
    }


  async  function update_quantity() {

        if (container.length < 1)
            return;

        await $.ajax({
            url: "<?php echo e(route('update_audited_notes')); ?>",
            type: "POST",
            'headers': {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
            data: {container},
            cache: false,
                success: function(dataResult){
            }
        });

        if (stockin_updates_openitemprod.length > 0) {
            let table_data = [];

           await $.ajax({
                url: "<?php echo e(route('openitem.openitem_stockin_update')); ?>",
                type: "POST",
                    'headers': {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                data: {table_data : stockin_updates_openitemprod, stock_type:"IN"},
                cache: false,
                success: function(dataResult){
                    // Clear the stored values
                    stockin_updates_openitemprod = [];
                }
            });
        }

         if (stockin_updates_locationprod.length > 0) {
            let table_data = [];

            await $.ajax({
                url: "<?php echo e(route('franchise.location_price.stockUpdate')); ?>",
                type: "POST",
                    'headers': {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                data: {table_data :stockin_updates_locationprod, stock_type:"IN"},
                cache: false,
                success: function(dataResult){
                    // Clear the stored values
                    stockin_updates_locationprods = [];
                }
            });
        }
         if (stockout_updates_openitemprod.length > 0) {
             let table_data = [];

            await $.ajax({
                url: "<?php echo e(route('openitem.openitem_stockout_update')); ?>",
                type: "POST",
                'headers': {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                data: {table_data : stockout_updates_openitemprod, stock_type:"OUT"},
                cache: false,
                success: function(dataResult){
                    stockout_updates_openitemprod = [];
                }
            });
        }
        if (stockout_updates_locationprod.length > 0) {
            let table_data = [];

            await $.ajax({
                url: "<?php echo e(route('franchise.location_price.stockUpdate')); ?>",
                type: "POST",
                'headers': {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                data: {table_data : stockout_updates_locationprod, stock_type:"OUT"},
                cache: false,
                success: function(dataResult){
                    stockout_updates_locationprod = [];
                }
            });
        }

        messageModal("Audited note created successfully");
        isConfirmEnabled++;
        detect_button();
        container = [];
        tableData ={}
        tablereport.ajax.reload();
    }

    function detect_button(){


        if (qtyincr > 0 && isConfirmEnabled !=1) {
            $("#confirm_update").removeAttr('disabled');
            $("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
            $("#confirm_update").css('cursor','pointer');
        } else {
            $("#confirm_update").attr('disabled', true);
            $("#confirm_update").css('background','gray');
            $("#confirm_update").css('cursor','not-allowed');
        }
    }


    function messageModal(msg){
        $('#modalMessage').modal('show');
        $('#statusModalLabelMsg').html(msg);
        setTimeout(function(){
            $('#modalMessage').modal('hide');
        }, 2500);
    }

</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/cstore_audited_rpt/cstore_audited_report.blade.php ENDPATH**/ ?>