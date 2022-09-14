<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'OPOSsum') }}</title>

<style>
body {
	margin: 0;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
	color: #212529;
	text-align: left;
	background-color: #fff;
}
.bg-refund{
	color:#fff;
	background:#ff7e30;
	border-color:#ff7e30
}
.table{
	width: 100%!important;
	border-style: none;
}
.thead-dark {
	color: white;
	border-color: #343a40;
	background-color: #343a40;

}
#table-th th{
	font-size: 12px!important;
}
.text-center{
	text-align: center;
}
.text-left{
	text-align: left;
}
.text-right{
	text-align: right;
}
.table-td td{
	font-size: 12px!important;
}
p {
	margin-top: 0;
	margin-bottom: 0rem;
	font-size: 12px;
}

hr{
	margin-bottom: 0;
}
#item tr  td {
	padding-top: 6px !important;
	padding-bottom: 6px !important;
	vertical-align: middle !important;
}
#item tr th{
	font-size: 12px;
	padding-top: 8px !important;
	padding-bottom: 8px !important;
	vertical-align: middle !important;
}

td{
	border-style: none;
}
th{
	border-style: none;
}

.text-bold {
	font-weight: bold;
	font-size: 12px;
}

span {
	font-size: 12px;
}

tr td span{
	/*width: 80px !important;*/
	/*height: 60px !important;*/
	text-align: center;
	vertical-align: middle;
	font-size: 12px;
	cursor: pointer;
	padding: 10px 20px;
	color: black;
	display: inline-block;
	font-weight: 400;
	margin-top: 10px;
}
.active{
	border-radius: 10px;
	color: white;
	padding: 10px 25px;
	background-color: black;
}
.rad-info-box .heading {
	font-size: 1.2em;
	font-weight: 300;
	text-transform: uppercase;
}
</style>
</head>
{{--{{ $requestValue['button_filter'] }}--}}
<body>
	<table border="0" style="width:100%; border-collapse: collapse"
		cellspacing="0" cellpadding="0">
		<tr>
			<td valign="center" rowspan="2" colspan="2">
			<b style="font-size:30px;font-weight:700;word-wrap:normal;">

      Cost Value Report
			</b>
			</td>
			<td valign="bottom" colspan="3" align="right"
				style="font-size: 15px">
				{{$location->name}}<br>{{$location->systemid}}
			</td>
		</tr>

		<tr>
			<td valign="bottom" colspan="5" align="right" >
				<p style="font-weight: 700;font-size: 12px">
				@if(!empty($requestValue['start_date']) && !empty($requestValue['end_date']))
					{{ date('M Y',strtotime($requestValue['start_date']))}}
				@endif
				</p>
			</td>
		</tr>
	</table>

    <table border="0" cellpadding="0" cellspacing="0" class="table" id="item" style="margin-top: 5px; width:100%">
        <thead class="thead-dark"  >
        <tr id="table-th" style="border-style: none">
            <th valign="middle" class="text-center" style="width:5%;">No</th>
            <th valign="middle" class="text-center" style="width:20%;">Barcode</th>
            <th valign="middle" class="text-left" style="padding-left:5px;width:30%;"> Product Name</th>
            <th valign="middle" class="text-center" style="width:8%;"> Price</th>
            <th valign="middle" class="text-center" style="width:8%;"> Cost</th>
            <th valign="middle" class="text-center" style="width:8%;">Qty</th>
            <th valign="middle" class="text-center" style="width:8%%;" colspan="6">Cost Value</th>
        </tr>
        </thead>

        <?php
$grandTotal = 0;
?>
        @foreach($openitem_prod as $key => $product)

                <tr class="table-td">
                    <td class="text-center" style="border-style: none">
                        {{$key + 1}}
                    </td>
                    <td class="text-center" style="border-style: none">

                        @php
                            $barcodes =  str_replace( array( '[',']', '"'), ' ', explode(",",$product->barcode));;

                        @endphp
                        @foreach ($barcodes as $item)
                            <p>{{$item}}</p>
                        @endforeach
                        {{-- $product->systemid --}}

                    </td>
                    <td class="text-left" style="border-style: none;padding-left:5px;">
                        {{$product->name}}
                    </td>
                     <td style="text-align:center;border-style: none;width:12%;">

                        {{ number_format($product->Iprice/100,2) }}

                    </td>
                     <td style="text-align:center;border-style: none;width:12%;">

                        {{ number_format($product->Icost/100,2) }}

                    </td>
                    <td style="text-align:center;border-style: none;width:5%;">

                        {{ number_format($product->Iqty)}}

                    </td>

                    <td style="text-align:right;border-style: none;width:12%;padding-right:5px;" colspan="6">
                            {{ number_format(($product->Icost * $product->Iqty) /100,2) }}
                    </td>
                    @php
                        $grandTotal += ($product->Icost * $product->Iqty);
                    @endphp
                </tr>

            </tr>
        @endforeach

        <tr>
            <td colspan="12" valign="middle">
                <div  style="border-top: 1px solid #a0a0a0;"></div>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="left" valign="middle">
                <p style="text-decoration-line: none;font-size: 12px;padding: 0;margin: 0;margin-top: -5px; ">
                     {{date('dMy h:i:s')}}
                </p>
            </td>
            <td colspan="9" align="right" valign="middle">
                <p style="text-decoration-line: none;font-size: 15px;font-weight: 700;padding: 0;margin-top: -5px; ">
                    Total {{ $currency }} {{ number_format($grandTotal /100,2) }}
                </p>
            </td>
        </tr>
    </table>

</body>
</html>
