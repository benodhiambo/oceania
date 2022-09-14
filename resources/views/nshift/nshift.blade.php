@extends('common.web')
@section('styles')

<script type="text/javascript" src="{{asset('js/console_logging.js')}}"></script>
<script type="text/javascript" src="{{asset('js/qz-tray.js')}}"></script>
<script type="text/javascript" src="{{asset('js/opossum_qz.js')}}"></script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: black !important;
	font-weight: normal !important;
}

#receipt-table_length, #receipt-table_filter,
#receipt-table_info, .paginate_button {
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

table.dataTable th.dt-right, table.dataTable td.dt-right {
	text-align: right !important;
}

td {
	vertical-align: middle !important;
	text-align: center !important;
}
tr:hover,  tr:hover > .sorting_1{
	background: none !important;
}

table.dataTable.display tbody tr.odd > .sorting_1, table.dataTable.order-column.stripe tbody tr.odd > .sorting_1 {
    background: none !important;
}

table.dataTable.order-column tbody tr > .sorting_1, table.dataTable.order-column tbody tr > .sorting_2, table.dataTable.order-column tbody tr > .sorting_3, table.dataTable.display tbody tr > .sorting_1, table.dataTable.display tbody tr > .sorting_2, table.dataTable.display tbody tr > .sorting_3 {
    background-color: #fff !important;
}
/** */
.receipt-item-l {
    text-align: left;
    padding-right: 0;
    font-size: 12px;
}

.receipt-item-c {
    padding-right: 0;
    padding-left: 0;
    font-size: 12px;
}

.receipt-item-discount {
    text-align: center;
    padding-right: 0;
    padding-left: 20px;
    font-size: 12px;
}

.receipt-item-r {
    text-align: right;
    padding-left: 0;
    font-size: 12px;
}

.void-stamp {
    font-size: 100px;
    color: red;
    position: absolute;
    z-index: 2;
    font-weight: 500;
    /* margin-top:50%; */
    margin-left: 10%;
    transform: rotate(45deg);

}

.col-pd {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.receipt_input {
    border: 1px solid black;
    border-radius: 7px;
    width: 80%;
    text-align: right;
    padding-right: 3;
    margin-bottom: 5px;
}

</style>
@endsection

@section('content')
@include('common.header')
@include('common.menubuttons')
<div class="modal fade" id="nShiftModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered mw-75" style="width:370px;" role="document">
        <div id="eodSummaryListModal-table-div" class="modal-content bg-purplelobster" style="width:370px; border-radius:10px;">
        <!--Modal EoD Summary-->

            <!--Modal Body Starts-->
            <div class="modal-body" style="font-size: 14px; font-weight: bold;">
                <!--Section 1 starts-->
                <!-- <div class="row">
                    <div class="col-md-7 pr-0">
                        <strong>Shift Summary</strong>
                    </div>
                    <div class="col-md-5 pl-1 text-right">
                        <strong>
                            16Jun22 23:59:59
                        </strong>
                    </div>
                </div> -->

                <!-- <hr style="border: 0.5px solid #a0a0a0;
                    margin-bottom:5px !important;
                    margin-top:5px !important"> -->

                <div class="row">
                    <div class="col-md-6">
                        <h3>Key In</h3>
                    </div>
                    <div class="col-md-2">
                        <strong class="global_currency"></strong>
                    </div>
                    <div class="col-md-4" style="text-align: right; font-size:17px">
                    </div>
                </div>

                <hr style="border: 0.5px solid #a0a0a0;
                    margin-bottom:5px !important;
                    margin-top:5px !important">

                <!--
                <hr style="border: 0.5px solid #a0a0a0;
                    margin-bottom:5px !important;
                    margin-top:5px !important"/>
                -->

                <div class="row">
                    <div class="col-md-8" style="font-weight: normal">
                        Non-Operational Cash In
                    </div>


                    <div class="col-md-4" style="text-align: right;">
                        <input class="receipt_input" type="text" value="0.00" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8" style="font-weight: normal;">
                        (-) Non-Operational Cash Out
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <input class="receipt_input" type="text" value="0.00" />
                    </div>
                </div>

                <div class="row" style="font-weight: normal">
                    <div class="col-md-8">
                        (-) Sales Drop
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <input class="receipt_input" type="text" value="0.00" />
                    </div>
                </div>

                <div class="row" style="font-weight: normal">
                    <div class="col-md-8">
                        Actual Drawer Amount
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <input class="receipt_input" type="text" value="0.00" />
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div id="loadOverlay" style="background-color: white; position:absolute; top:0px; left:0px;
	width:100%; height:100%; z-index:2000;">
</div>
<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center"
			style="display:flex;height:75px">
			<div class="col" style="width:70%">
				<h2 style="margin-bottom: 0;">
				Shift
				</h2>
			</div>
			<div class="col-md-2">
				<h5 style="margin-bottom:0">{{ $location->name??"" }}</h5>
				<h5 style="margin-bottom:0">{{ $location->systemid??"" }}</h5>
			</div>
			<div class="middle;col-md-3">
				<h5 style="margin-bottom:0;">Terminal ID: {{ $terminal->systemid??"" }}</h5>

			</div>
			<div class="col-md-2 text-right">
				<h5 style="margin-bottom:0;"></h5>
			</div>
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display"
			   id="eodsummarylist" style="width:100%;">
			<thead class="thead-dark">
			<tr>
				<th class="text-center" style="width:30px;">No</th>
				<th class="text-center" style="width:200px">In</th>
				<th class="text-center" style="width:200px;">Out</th>
				<th class="text-center" style="width:120px;">Staff ID</th>
				<th class="text-left" style="width:auto">Staff Name</th>
				<th class="text-center" style="width:50px">Key In</th>
			</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>
</div>

<div id="res"></div>



<style>
.btn {
	color: #fff !Important;
}

.form-control:disabled, .form-control[readonly] {
	background-color: #e9ecef !important;
	opacity: 1;
}

#void_stamp {
	font-size: 100px;
	color: red;
	position: absolute;
	z-index: 2;
	font-weight: 500;
	margin-top: 130px;
	margin-left: 10%;
	transform: rotate(45deg);
	display: none;
}
/* #loadOverlay{display: none;} */
</style>
@section ('script')
<script>
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});


function atm_money(num) {
	if (num.toString().length == 1) {
		return '0.0' + num.toString()

	} else if (num.toString().length == 2) {
		return '0.' + num.toString()

	} else if (num.toString().length == 3) {
		return num.toString()[0] + '.' + num.toString()[1] +
			num.toString()[2];

	} else if (num.toString().length >= 4) {
		return num.toString().slice(0, (num.toString().length - 2)) +
			'.' + num.toString()[(num.toString().length - 2)] +
			num.toString()[(num.toString().length - 1)];
	}
}


function getOptlist(id) {
	$.ajax({
		method: "post",
		url: "{{route('local_cabinet.optList')}}",
		data: {id: id}
	}).done((data) => {
		$("#optlistModal-table").html(data);
		$("#optlistModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}

function openShiftModal(id)
{
    $("#nShiftModal").modal('show');
}


function getEvReceiptlist(id) {
	$.ajax({
		method: "post",
		url: "{{route('ev_receipt.evList')}}",
		data: {id: id}
	}).done((data) => {
		$("#evlistModal-table").html(data);
		$("#evlistModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}


function print_eod() {
    $.ajax({
        url: "/eod_print",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'post',
        data:{
            'eod_date':'16Jun22',
        },
        success: function (response) {
            var error1=false, error2=false;
            console.log('PR ',(response));

            try {
                eval(response);
                console.log('print_eod: eval() working');
            } catch (exc) {
                error1 = true;
                console.error('ERROR eval(): '+
					JSON.stringify(exc));
            }

            if (!error1) {
				try {
                    escpos_print_template();
                    console.log('print_eod: escpos_print_template() working');
                } catch (exc) {
                    error2 = true;
                    console.error('ERROR escpos_print_template(): '+
						JSON.stringify(exc));
                }
            }
        },
        error: function (e) {
            console.log('PR '+JSON.stringify(e));
        }
    });
}

$(document).ready(function() {
	$("#loadOverlay").css("display","none");
    var tbl_data = {
        'date': '{{ $date }}',
    }
	var eodtable = $('#eodsummarylist').DataTable({
		// "processing": true,
		"serverSide": true,
		async: false,
        "ajax": {
            "url": "{{ route('local_cabinet.nshift.datatable') }}",
            "type": "POST",
            data: function(d) {
                return $.extend(d, tbl_data);
            },
            'headers': {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{
				data: 'in',
				name: 'in',
				orderable: true,
				searchable: true
			},
			{
				data: 'out',
				name: 'out',
				orderable: true,
				searchable: true
			},
			{
				data: 'staff_id',
				name: 'staff_id',
				orderable: true,
				searchable: true
			},
			{
				data: 'staff_name',
				name: 'staff_name',
				orderable: true,
				searchable: true
			},
			{
				data: 'action',
				name: 'action',
                orderable: false
			},
			// {data: 'email', name: 'email'},
			// {data: 'username', name: 'username'},
			// {data: 'phone', name: 'phone'},
			// {data: 'dob', name: 'dob'},
			// {
			// 	data: 'action',
			// 	name: 'action',
			// 	orderable: true,
			// 	searchable: true
			// },
		],
        "columnDefs": [
		    { "className": "text-left dt-left", "targets": [4]}
        ]
	});

	// window.addEventListener('storage', (e) => {
	//     switch (e.key) {
	//         case "reload_for_lc":
	// 			eodtable.ajax.reload();
	// 			localStorage.removeItem('reload_for_lc');
	// 			console.log("lc reload.")
	//             break;
	// 	}
	// });

});
$('.sorting_1').css('background-color', 'white');



// function eod_summarylist(eod_date) {
// 	$.ajax({
// 		url: "{{route('local_cabinet.eodsummary.popup')}}/" + eod_date,
// 		// headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
// 		type: 'get',
// 		success: function (response) {
// 			// console.log(response);
// 			$('#eodSummaryListModal-table-div').html(response);
// 			$('#eodSummaryListModal').modal('show').html();
// 		},
// 		error: function (e) {
// 			$('#responseeod').html(e);
// 			$("#msgModal").modal('show');
// 		}
// 	});
// }


// function receipt_list(date) {
// 	$.ajax({
// 		url: "{{route('local_cabinet.receipt.list')}}",
// 		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
// 		type: 'post',
// 		data: {
// 			date
// 		},
// 		success: function (response) {
// 			//console.log(response);
// 			$('#receiptoposModal-table').html(response);
// 			$('#receiptoposModal').modal('show').html();

// 			$('#receipt-table').DataTable({
// 				"order": [],
// 				"columnDefs": [
// 					{"targets": -1, 'orderable': false}
// 				],
// 				"autoWidth": false,
// 			})
// 		},
// 		error: function (e) {
// 			$('#responseeod').html(e);
// 			$("#msgModal").modal('show');
// 		}
// 	});
// }



</script>
@endsection
@extends('common.footer')
