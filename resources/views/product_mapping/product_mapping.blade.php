@extends('common.web')
@section('styles')

<script type="text/javascript" src="{{ asset('js/console_logging.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/qz-tray.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/opossum_qz.js') }}"></script>

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
button:focus{
    outline: 0;
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


.form-group select.form-control {
  display: inline-block;
  width: 280px !important;
   padding:-1.2% !important;
   margin-bottom:-2.5% !important;
   font-size: 11px !important;
   margin-left:2% !important;

}
</style>
@endsection



@section('content')
@include('common.header')
@include('common.menubuttons')
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		<div class="d-flex"
			style="width:100%;margin-bottom: 5px; margin-top:5px">
			<div class="col-md-10 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Product Mapping</h2>
			</div>

                <div class="col-md-2 d-flex pr-0"
					style="justify-content:flex-end">
                    <button class="btn btn-success bg-save  sellerbutton mr-0 btn-sq-lg"
						onclick="update_mapping()"
						style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Save
					</button>

			</div>
			
		</div>

        <div class="row">
            @for ($k=1;$k<=4; $k++)
            <div class="col-3 mb-5">
                 <h5 class="mb-2" >
					Pump {{ $k }}
					
				</h5>
                @for($i=1; $i<=6; $i++)
                
                <?php 
					$pmp = $pumps->firstWhere('pump_no', $i);
					
					if(!empty($pmp)) {
						
						$nzl = $pmp->nozzles->firstWhere('lpz_nozzle_no', $i);
					
						if(!empty($nzl)) {
							$ogfuelid = $nzl->lpz_ogfuel_id;
							
							
						} else {
							$ogfuelid = 0;
						}
					} 
					
				?>

                <form class="">

                     <div class="form-group" style="">
                        <label class="control-label">Nozzle {{ $i }} </label>
                        <div class="">
                            <select class="form-control" autocomplete="off"  id="select_pmp_{{ $k }}_n_{{ $i }}">
                                <option value="{{ $k }}_{{ $i }}_0"></option>

                               @foreach($products as $prd)
                               		@if($prd->og_fuel_id == $ogfuelid)
                               		<option  selected="selected"
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@else
                               		<option 
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@endif
                               @endforeach

                            </select>
                        </div>
                    </div>
                </form>
                @endfor
            </div>
            @endfor
        </div>
         <div class="row">
            @for ($k=5;$k<=8; $k++)
            <div class="col-3 mb-5">
                 <h5 class="mb-2" >
					Pump {{ $k }}
					
				</h5>
                @for($i=1; $i<=6; $i++)
                
                <?php 
					$pmp = $pumps->firstWhere('pump_no', $i);
					
					if(!empty($pmp)) {
						
						$nzl = $pmp->nozzles->firstWhere('lpz_nozzle_no', $i);
					
						if(!empty($nzl)) {
							$ogfuelid = $nzl->lpz_ogfuel_id;
							
							
						} else {
							$ogfuelid = 0;
						}
					
					} 
					
				?>

                <form class="">

                     <div class="form-group" style="">
                        <label class="control-label">Nozzle {{ $i }} </label>
                        <div class="">
                            <select class="form-control" autocomplete="off"  id="select_pmp_{{ $k }}_n_{{ $i }}">
                                <option value="{{ $k }}_{{ $i }}_0"></option>

                               @foreach($products as $prd)
                               		@if($prd->og_fuel_id == $ogfuelid)
                               		<option  selected="selected"
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@else
                               		<option 
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@endif
                               @endforeach

                            </select>
                        </div>
                    </div>
                </form>
                @endfor
            </div>
            @endfor
        </div>
         <div class="row">
            @for ($k=9;$k<=12; $k++)
            <div class="col-3 mb-5">
                 <h5 class="mb-2" >
					Pump {{ $k }}
					
				</h5>
                @for($i=1; $i<=6; $i++)
                
                <?php 
					$pmp = $pumps->firstWhere('pump_no', $i);
					
					if(!empty($pmp)) {
						
						$nzl = $pmp->nozzles->firstWhere('lpz_nozzle_no', $i);
					
						if(!empty($nzl)) {
							$ogfuelid = $nzl->lpz_ogfuel_id;
							
							
						} else {
							$ogfuelid = 0;
						}
					
					} 
					
				?>

                <form class="">

                     <div class="form-group" style="">
                        <label class="control-label">Nozzle {{ $i }} </label>
                        <div class="">
                            <select class="form-control" autocomplete="off"  id="select_pmp_{{ $k }}_n_{{ $i }}">
                                <option value="{{ $k }}_{{ $i }}_0"></option>

                               @foreach($products as $prd)
                               		@if($prd->og_fuel_id == $ogfuelid)
                               		<option  selected="selected"
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@else
                               		<option 
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@endif
                               @endforeach

                            </select>
                        </div>
                    </div>
                </form>
                @endfor
            </div>
            @endfor
        </div>
         <div class="row">
            @for ($k=13;$k<=16; $k++)
            <div class="col-3 mb-5">
                 <h5 class="mb-2" >
					Pump {{ $k }}
					
				</h5>
                @for($i=1; $i<=6; $i++)
                
                <?php 
					$pmp = $pumps->firstWhere('pump_no', $i);
					
					if(!empty($pmp)) {
						
						$nzl = $pmp->nozzles->firstWhere('lpz_nozzle_no', $i);
					
						if(!empty($nzl)) {
							$ogfuelid = $nzl->lpz_ogfuel_id;
							
							
						} else {
							$ogfuelid = 0;
						}
					
					} 
					
				?>

                <form class="">

                     <div class="form-group" style="">
                        <label class="control-label">Nozzle {{ $i }} </label>
                        <div class="">
                            <select class="form-control" autocomplete="off"  id="select_pmp_{{ $k }}_n_{{ $i }}">
                                <option value="{{ $k }}_{{ $i }}_0"></option>

                               @foreach($products as $prd)
                               		@if($prd->og_fuel_id == $ogfuelid)
                               		<option  selected="selected"
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@else
                               		<option 
                                   		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
                                       {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                   	</option>
                               		@endif
                               @endforeach

                            </select>
                        </div>
                    </div>
                </form>
                @endfor
            </div>
            @endfor
        </div>
         <div class="row">
            @for ($k=17;$k<=20; $k++)
            <div class="col-3 mb-5">
                 <h5 class="mb-2" >
					Pump {{ $k }}
					
				</h5>
                @for($i=1; $i<=6; $i++)
                
                <?php 
                
                
					$pmp = $pumps->firstWhere('pump_no', $k);
					
					if(!empty($pmp)) {
						
						$nzl = $pmp->nozzles->firstWhere('lpz_nozzle_no', $i);
					
						if(!empty($nzl)) {
					
							$ogfuelid = $nzl->lpz_ogfuel_id;
							
						} else {
							$ogfuelid = 0;
						}
					
					} 
					
				?>

                <form class="">

                     <div class="form-group" style="">
                        <label class="control-label">Nozzle {{ $i }} </label>
                        <div class="">
                            <select class="form-control" autocomplete="off"  id="select_pmp_{{ $k }}_n_{{ $i }}">
                                <option value="{{ $k }}_{{ $i }}_0"></option>

								@foreach($products as $prd)
                               
		                           <?php 
				                       	if($k == 18) {
								
											\Log::debug([
													'pump no.........' => $k,
													'nozzle no.......' => $i,
													'$ogfuelid.......' => $ogfuelid,
													'$prd->og_fuel_id' => $prd->og_fuel_id
												]);
										
										} 
		                           
		                           ?>
                               		@if($prd->og_fuel_id == $ogfuelid)
		                           		<option  selected="selected"
		                               		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
		                                   {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
		                               	</option>
                               		@else
		                           		<option 
		                               		value="{{ $k }}_{{ $i }}_{{$prd->og_fuel_id}}">
		                                   {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
		                               	</option>
                               		@endif
                               @endforeach

                            </select>
                        </div>
                    </div>
                </form>
                @endfor
            </div>
            @endfor
        </div>


    </div>
</div>


<script type="text/javascript">

var update_array = []
$("select[id^='select_pmp_']").change(function(){
	
	// get nozzle info
	let nozzle_data = $(this).val().split("_");
	
    
    let nozzle_map = {
        pump_id: nozzle_data[0],
        nozzle_no: nozzle_data[1],
        ogfuel_id: nozzle_data[2],
    }
    
    if(update_array != undefined &&
		update_array != null &&
		update_array != '') {
		
		/*
		If this mapping is already defined, remove it.
		*/
		update_array = update_array.filter(function(me) {
			if(!((me.pump_id == nozzle_map.pump_id) && (me.nozzle_no == nozzle_map.nozzle_no))) {
				return me;
			}
		});
		
		update_array.push(nozzle_map)
	
	} else {
		// New array so just push values
		console.log(`Object 1`, JSON.stringify(nozzle_map))
			
		update_array.push(nozzle_map)
	}
    
	
    
	console.log('update_array=', JSON.stringify(update_array))

})

function update_mapping() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if (update_array != undefined &&
        update_array != null &&
        update_array != '') {

        update_array.forEach(element => {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                    method: "post",
                    url: "{{ route('product_mapping.save_mapping') }}",
                    data: element,
                }).done((data) => {
                    console.log('**mapping saved**', data)
                })
                .fail((data) => {
                    console.log('**mapping failed**', data)
                });
        });
        update_array = []
    }

}

$(document).ready(function() {
    update_array = []
})
</script>

@endsection
@extends('common.footer')
