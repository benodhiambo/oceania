
<table>
     <thead>
     <tr>
        <th>{{ date('dMy',strtotime($start_date)) .'-'.  date('dMy',strtotime($stop_date)) }}</th>
    </tr>
    </thead>
    <thead>
    <tr>
        <th> C-Store Receipt List</th>

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
        <th>Refund</th>
         <th>Void</th>
         @foreach($product_name as $v)
        <th >{{ $v->name }}</th>
        @endforeach

        <th>Cash</th>
        <th>Credit Card</th>
        <th>Wallet</th>
        {{-- <th>Credit Account</th> --}}

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
    @foreach($cstore_products as $in =>$i)
        <tr>
            <td>{{$in + 1}}</td>
            <td>{{ date('dMy H:i:s',strtotime($i->date)) }}</td>
            <td>{{ $i->receipt_i }}</td>
            <td style='text-align: right;'>{{ number_format(($i->total-$i->Refund)/100,2) }}</td>
            <td style='text-align: right;'>{{ number_format($i->Refund/100,2) }}</td>
            <td style=''>{{ number_format($i->void,2) }}</td>
             @if(in_array($i->product_name,$newPrdList ))
                @php
                     $length = count($product_name);
                    $position = array_search($i->product_name,$newPrdList );
                    tdgenerator($position, ($i->quantity * $i->price),$length);
                //  print_r($length);
                @endphp
            @endif

            <td style='text-align: right;'>{{  number_format($i->cash_received / 100,2) }}</td>
            <td style='text-align: right;'>{{ number_format( $i->creditcard/100,2)}}</td>
            <td style='text-align: right;'>{{number_format( $i->wallet/100,2) ?? ''}}</td>
            {{-- <td style='text-align: right;'>{{ number_format($i->ca/100,2) ?? ''}}</td> --}}
        </tr>
    @endforeach
    </tbody>
</table>
