
<table>
    <thead>
     <tr>
         <th></th>
         <th><?php echo e(date('dMy',strtotime($start_date))); ?> - <?php echo e(date('dMy',strtotime($stop_date))); ?></th>
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
        <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th ><?php echo e($v->name); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <th >Cash</th>
        <th >Credit Card</th>
        <th >Wallet</th>
        <th >Credit Account</th>

    </tr>
    </thead>
    <tbody>
        <?php

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
        ?>
    <?php $__currentLoopData = $receiptList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in =>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td ><?php echo e($in + 1); ?></td>
            <td><?php echo e(date('dMy H:i:s',strtotime($i->date))); ?></td>
            <td><?php echo e($i->receipt_i); ?></td>
            <td><?php echo e(number_format(($i->total - $i->refund )/100,2)); ?></td>
            <td><?php echo e(number_format( $i->fuel /100,2)); ?></td>
            <td><?php echo e(number_format($i->filled/100,2)); ?></td>
            <td><?php echo e(number_format( $i->refund/100,2)); ?></td>
            <?php if(in_array($i->product_name,$newPrdList )): ?>
                <?php
                     $length = count($product_name);
                    $position = array_search($i->product_name,$newPrdList );
                    tdgenerator($position, ($i->quantity * $i->price),$length);
                //  print_r($length);
                ?>
            <?php endif; ?>

            <td><?php echo e(number_format($i->cash_received /100,2)); ?></td>
            <td><?php echo e(number_format($i->creditcard /100,2)); ?></td>
            <td><?php echo e(number_format($i->wallet/100,2)); ?></td>
            <td><?php echo e(number_format( $i->creditac/100,2)); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH /home/dev/oceania/trunk/oceania/resources/views/excel_export/fuel_receiptlist_excel.blade.php ENDPATH**/ ?>