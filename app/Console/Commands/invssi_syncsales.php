<?php

namespace App\Console\Commands;

use \App\Classes\InvSSI;
use \App\Models\invssi_sale;
use \App\Models\invssi_saleitems;
use Illuminate\Console\Command;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Nelexa\Buffer\Buffer;
use Nelexa\Buffer\StringBuffer;



class invssi_syncsales extends Command
{

	// Crontab record for running the syncsales every 5 mins
	// */5 * * * * cd /home/user/oceania/trunk/oceania;php artisan invssi:syncsales  >> /home/user/log/syncsales.log 2>&1

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invssi:syncsales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Invenco OPT Sales Transaction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		/* Initialize variables */

		dump('Starting Invenco OPT Sales Transaction Synchronization....');
		$ssi = new InvSSI;

		/*
		$ret = $ssi->get_all_terminals();
		dump($ret);

		$ret = $ssi->get_all_terminal_status();
		dump($ret);
		*/

		$data = $ssi->get_completed_sales();
		dump($data);

		if (!empty($data) && !empty($data['response'])) {

			foreach ($data['response'] as $sale) {
				dump('saleId='.$sale->saleId);

				// Store each transaction details to DB
				$db = new invssi_sale;
				$db->saleId		=
					empty($sale->saleId)?null:$sale->saleId;
				$db->terminalId	=
					empty($sale->terminalId)?null:$sale->terminalId;
				$db->mop		=
					empty($sale->mop)?null:$sale->mop;
				$db->cardType	=
					empty($sale->cardType)?null:$sale->cardType;
				$db->receipt	=
					empty($sale->receipt)?null:$sale->receipt; 
				$db->amount		=
					empty($sale->amount)?null:$sale->amount;
				$db->originalAmount	=
					empty($sale->originalAmount)?null:$sale->originalAmount;
				$db->cardBalance =
					empty($sale->cardBalance)?null:$sale->cardBalance;
				$db->loyaltyId =
					empty($sale->loyaltyId)?null:$sale->loyaltyId;
				$db->loyaltyAccountCode =
					empty($sale->loyaltyAccountCode)?null:$sale->loyaltyAccountCode;
				$db->pumpedBalance =
					empty($sale->pumpedBalance)?null:$sale->pumpedBalance;
				$db->pointsEarned =
					empty($sale->pointsEarned)?null:$sale->pointsEarned;
				$db->pointsRedeemed =
					empty($sale->pointsRedeemed)?null:$sale->pointsRedeemed;
				$db->pointsBalanced =
					empty($sale->pointsBalanced)?null:$sale->pointsBalanced;
				$db->mainStatus =
					empty($sale->mainStatus)?null:$sale->mainStatus;
				$db->subStatus =
					empty($sale->subStatus)?null:$sale->subStatus;
				$db->storeId =
					empty($sale->storeId)?null:$sale->storeId;
				$db->transactionTime =
					empty($sale->transactionTime)?null:$sale->transactionTime;
				$db->transactionType =
					empty($sale->transactionType)?null:$sale->transactionType;
				$db->uuid		=
					empty($sale->uuid)?null:$sale->uuid;
				$db->batchId	=
					empty($sale->batchId)?null:$sale->batchId;
				$db->save();

				// Populate Sale Items in child table for 1:m relationship
				if (!empty($sale->saleItems)) {
					foreach ($sale->saleItems as $item) {
						dump($item);

						// Store each saleItem to DB
						$dbsi = new invssi_saleitems;
						$dbsi->saleId		= 
							empty($sale->saleId)?null:$sale->saleId;
						$dbsi->amount		= 
							empty($item->amount)?null:$item->amount;
						$dbsi->productId	= 
							empty($item->productId)?null:$item->productId;
						$dbsi->pumpId		= 
							empty($item->pumpId)?null:$item->amount;
						$dbsi->quantity		= 
							empty($item->quantity)?null:$item->quantity;
						$dbsi->unitPrice	= 
							empty($item->unitPrice)?null:$item->unitPrice;
						$dbsi->taxAmount	= 
							empty($item->taxAmount)?null:$item->taxAmount;
						$dbsi->originalAmount = 
							empty($item->originalAmount)?null:$item->originalAmount;
						$dbsi->save();

						// Populate discount in child table for 1:m relation
						if (!empty($item->discount)) {
							foreach ($item->discount as  $disc) {
								$dbdisc = new invssi_discount;
								$dbdisc->item_id	= 
									empty($dbsi->id)?null:$dbsi->id;
								$dbdisc->discountTransactionId	=
									empty($disc->discountTransactionId)?null:$disc->discountTransactionId;
								$dbdisc->discountName	=
									empty($disc->discountName)?null:$disc->discountName;
								$dbdisc->discountToken	=
									empty($disc->discountToken)?null:$disc->discountToken;
								$dbdisc->discountType	=
									empty($disc->discountType)?null:$disc->discountType;
								$dbdisc->discountValue	=
									empty($disc->discountValue)?null:$disc->discountValue;
								$dbdisc->discountAmount	=
									empty($disc->discountAmount)?null:$disc->discountAmount;
								$dbdisc->discountQty	=
									empty($disc->discountQty)?null:$disc->discountQty;
								$dbdisc->promotionId	=
									empty($disc->promotionId)?null:$disc->promotionId;
								$dbdisc->redemptionId	=
									empty($disc->redemptionId)?null:$disc->redemptionId;
								$dbdisc->applyDiscount	=
									empty($disc->applyDiscount)?null:$disc->applyDiscount;
								$dbdisc->processedOffline =
									empty($disc->processedOffline)?null:$disc->processedOffline;
								$dbdisc->save();
							}
						}
					}
				}

				// Populate Loyalty Details in child table for 1:m relationship
				if (!empty($sale->loyaltyDetails)) {
					foreach ($sale->loyaltyDetails as $ld) {
						dump($ld);

						// Store each Loyalty Detail to DB
						$dbld = new invssi_loyaltydetails;
						$dbld->saleId			= 
							empty($sale->saleId)?null:$sale->saleId;
						$dbld->pointsBalance	=
							empty($ld->pointsBalance)?null:$ld->pointsBalance;
						$dbld->issuedPoints		=
							empty($ld->issuedPoints)?null:$ld->issuedPoints;
						$dbld->bonusPoints		=
							empty($ld->bonusPoints)?null:$ld->bonusPoints;
						$dbld->cardNumber		=
							empty($ld->cardNumber)?null:$ld->cardNumber;
						$dbld->save();
					}
				}

				// Populate GiftCardBalance in child table for 1:m relationship
				if (!empty($sale->giftCardBalance)) {
					foreach ($sale->giftCardBalance as $gcb) {
						dump($gcb);

						// Store each Loyalty Detail to DB
						$dbgcb = new invssi_giftcardbalance;
						$dbgcb->saleId			= 
							empty($sale->saleId)?null:$sale->saleId;
						$dbgcb->beforeTransBalance	= 
							empty($gcb->beforeTransBalance)?null:$gcb->beforeTransBalance;
						$dbgcb->afterTransBalance	= 
							empty($gcb->afterTransBalance)?null:$gcb->afterTransBalance;
						$dbgcb->save();
					}
				}

				// Finally we synchronize the saleId 
				$ret = $ssi->process_sale($sale->saleId);
				dump($ret);
			}
		}

        return 0;
    }
}
