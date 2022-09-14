
<table>
    <thead>
    <tr>
        <th> Full tank Receipt List</th>

        <th></th>
        <th></th>
        <th></th>
        <th></th>

    </tr>
    </thead>
    <thead>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Receipt ID</th>
        <th>Total</th>
         @foreach($product_name as $v)
        <th >{{ $v->name }}</th>
        @endforeach

        <th>Cash</th>
        <th>Credit Card</th>
        <th>Wallet</th>
        <th>Credit Account</th>

    </tr>
    </thead>
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
                        $front.= "<td style='text-align: right;'>".number_format(round($value /100),2)."</td>\n";
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
    <tbody>
    @foreach($receiptList as $in =>$i)
        <tr>
            <td>{{$in + 1}}</td>
            <td>{{ date('dMy H:i:s',strtotime($i->Fcreated_at)) }}</td>
            <td>{{ $i->systemid }}</td>
            <td style='text-align: right;'>{{ number_format($i->Total/100,2) }}</td>
             @if(in_array($i->product_name,$newPrdList ))
                @php
                     $length = count($product_name);
                    $position = array_search($i->product_name,$newPrdList );
                    tdgenerator($position, ($i->quantity * $i->price),$length);
                //  print_r($length);
                @endphp
            @endif

            <td style='text-align: right;'>{{  number_format($i->cash_received/ 100,2) }}</td>
            <td style='text-align: right;'>{{ $i->creditcard_no}}</td>
            <td style='text-align: right;'>{{ $i->wallet ?? ''}}</td>
            <td style='text-align: right;'>{{ $i->ca ?? ''}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
