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
                        <p style="margin-bottom: 0; font-size:13px; font-weight:bold;"> <?php echo e($location->name); ?></p>
                        <p style="margin-bottom: 0;font-size:13px;font-weight:bold;"> <?php echo e($location->systemid); ?></p>
                        <p style="margin-bottom: 0;font-size:13px;font-weight:bold;"></p>
                    </div>
                    <div class="col-md-3 pl-0 align-self-center" style="">
                        <p style="margin-bottom: 0;font-size:13px;font-weight:bold;"> <?php echo e($user->fullname); ?></p>
                        <p style="margin-bottom: 0;font-size:13px;font-weight:bold;"> <?php echo e($user->systemid); ?></p>
                        <p style="margin-bottom: 0;font-size:13px;font-weight:bold;"> </p>
                    </div>

                        <div class="col-md-2 d-flex pr-0"
                            style="justify-content:flex-end">
                            <button class="btn btn-success sellerbutton mr-0 btn-sq-lg bg-confirm-button"
                            onclick="update_quantity()" id="confirm"
                            style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Confirm
                        </button>


                    </div>
                </div>
                <div style="margin-top: 0">
                <table border="0" cellpadding="0" cellspacing="0" class="table " id="eodsummarylistd" style="margin-top: 0px; width:100%">
                    <thead class="thead-dark"  >
                    <tr id="table-th" style="border-style: none">
                        <th valign="middle" class="text-center" style="width:30px">No</th>
                        <th valign="middle" class="text-center" style="width:7%;">Product ID</th>
                        <th valign="middle" class="text-left" style="">Product Name</th>

                        <th valign="middle" class="text-center" style="width:7%;">Qty</th>
                        <th valign="middle" class="text-center" style="width:13.9%;">Audited Qty</th>
                        <th valign="middle" class="text-center" style="width:7%;">Difference</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $audited_report_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="table-td">
                            <td class="text-center" style="border-style: none">
                                <?php echo e($key+ 1); ?>

                            </td>
                            <td class="text-center" style="border-style: none;width:7%;" >
                                <span id="product_<?php echo e($key +1); ?>" ><?php echo e($product->systemid); ?></span>
                            </td>
                            <td class="text-left" style="border-style: none;width:auto;">
                            <img src="/images/product/thumb/<?php echo e($product->thumbnail_1); ?>" alt="imf" style="height:25px;width:25px;"> <?php echo e($product->name); ?>

                            </td>
                            <td class="text-center" style="border-style: none;width:6px;" id="qty_<?php echo e($key +1); ?>">
                                <?php echo e(number_format($product->Iqty)); ?>

                            </td>

                            <td class=" dt-right vt_middle slim-cell">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="align-self-center value-button increase" id="increase_<?php echo e($key +1); ?>" onclick="increaseValue('<?php echo e($key +1); ?>')" value="Increase Value">
                                            <ion-icon class="ion-ios-plus-outline" style="font-size: 24px;margin-right:5px;">
                                            </ion-icon>
                                        </div>

                                        <input type="number" id="number_<?php echo e($key +1); ?>" oninput="changeValueOnBlur('<?php echo e($key +1); ?>')" class="number product_qty js-product-qty" value="0" min="0" required="">

                                        <div class="value-button decrease" id="decrease_<?php echo e($key + 1); ?>" onclick="decreaseValue('<?php echo e($key + 1); ?>')" value="Decrease Value">
                                            <ion-icon class="ion-ios-minus-outline" style="font-size: 24px;margin-left:5px;">
                                            </ion-icon>
                                        </div>
                                    </div>
                            </td>
                            <td class="text-center" style="border-style: none;width:6px;">
                                <span id="diff_<?php echo e($key +1); ?>" ></span>
                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
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

    


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/user/oceania/trunk/oceania/resources/views/cstore_audited_rpt/audited_note.blade.php ENDPATH**/ ?>