
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
         <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th ><?php echo e($v->name); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <th>Cash</th>
        <th>Credit Card</th>
        <th>Wallet</th>
        <th>Credit Account</th>

    </tr>
    </thead>
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
        ?>
    <tbody>
    <?php $__currentLoopData = $receiptList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in =>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($in + 1); ?></td>
            <td><?php echo e(date('dMy H:i:s',strtotime($i->Fcreated_at))); ?></td>
            <td><?php echo e($i->systemid); ?></td>
            <td style='text-align: right;'><?php echo e(number_format($i->Total/100,2)); ?></td>
             <?php if(in_array($i->product_name,$newPrdList )): ?>
                <?php
                     $length = count($product_name);
                    $position = array_search($i->product_name,$newPrdList );
                    tdgenerator($position, ($i->quantity * $i->price),$length);
                //  print_r($length);
                ?>
            <?php endif; ?>

            <td style='text-align: right;'><?php echo e(number_format($i->cash_received/ 100,2)); ?></td>
            <td style='text-align: right;'><?php echo e($i->creditcard_no); ?></td>
            <td style='text-align: right;'><?php echo e($i->wallet ?? ''); ?></td>
            <td style='text-align: right;'><?php echo e($i->ca ?? ''); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH /home/user/oceania/trunk/oceania/resources/views/excel_export/fuel_fulltank_receiptlist_excel.blade.php ENDPATH**/ ?>