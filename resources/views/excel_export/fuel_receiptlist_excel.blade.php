
<table>
    <thead>
     <tr>
         <th></th>
         <th>{{ date('dMy',strtotime($start_date)) }} - {{ date('dMy',strtotime($stop_date)) }}</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>

    </tr>
    <thead>
     <tr>
         <th></th>
        <th>Fuel Receipt List</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>

    </tr>
    </thead>
    <thead>
    <tr>
        <th >No</th>
        <th >Date</th>
        <th >Receipt ID</th>
        <th >Total</th>
        <th >Fuel</th>
        <th >Filled</th>
        <th >Refund</th>
        @foreach($product_name as $v)
        <th >{{ $v->name }}</th>
        @endforeach

        <th >Cash</th>
        <th >Credit Card</th>
        <th >Wallet</th>
        <th >Credit Account</th>

    </tr>
    </thead>
    <tbody>
        @php

         $newPrdList = [];
          foreach ($product_name as $key => $value) {
              # code...
              array_push($newPrdList,$value->name);
          }
          function tdgenerator($position,$value,$len){
              $front = '';
               for ($f = 0;$f<=$position;$f++) {

                   if ($f == $position){
                        $front.= "<td >".number_format(round( $value /100),2)."</td>\n";
                   } else {
                        # code...
                         $front .=  "  <td> </td>\n";
                    }
               }
              for ($i = $position +1;$i<=$len-1;$i++) {
                 $front.= "  <td> </td>\n";
                }
            echo $front;
          }
        @endphp
    @foreach($receiptList as $in =>$i)
        <tr>
            <td >{{ $in + 1}}</td>
            <td>{{ date('dMy H:i:s',strtotime($i->date)) }}</td>
            <td>{{ $i->receipt_i}}</td>
            <td>{{ number_format(($i->total - $i->refund )/100,2) }}</td>
            <td>{{number_format( $i->fuel /100,2) }}</td>
            <td>{{ number_format($i->filled/100,2) }}</td>
            <td>{{number_format( $i->refund/100,2) }}</td>
            @if(in_array($i->product_name,$newPrdList ))
                @php
                     $length = count($product_name);
                    $position = array_search($i->product_name,$newPrdList );
                    tdgenerator($position, ($i->quantity * $i->price),$length);
                //  print_r($length);
                @endphp
            @endif

            <td>{{ number_format($i->cash_received /100,2) }}</td>
            <td>{{ number_format($i->creditcard /100,2)}}</td>
            <td>{{ number_format($i->wallet/100,2) }}</td>
            <td>{{ number_format( $i->creditac/100,2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
