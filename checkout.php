<?php 

class Checkout {

    protected $db_handle;

     /**
     * 
     *  @param handle :  DBController // use of typehinting 
     *  @return  db_handle : database handle
     */
    public function __construct(DBController $handle)
    {
         $this->db_handle = $handle;
    }

    /**
     * 
     *  @param product_id : int 
     *  @param qty : int
     *  @return  float discount price  : float
     *  use of sclare data type and return type
     */
    function getDiscountedPrice( int $product_id,  int $qty) : float {

        $insertedProduct = $this->db_handle->runQuery(" SELECT * FROM tbl_products LEFT JOIN tbl_specialoffers ON tbl_products.id = tbl_specialoffers.id WHERE tbl_products.id='" . $product_id . "'");
        
        for ($i = 0; $i < count($insertedProduct); $i++) {

            if ($qty == $insertedProduct[$i]['quantity']) {
                $special_price = $insertedProduct[$i]['special_price'];
            }

            //checked in input qty greater than sepcial offer qty 
            if ($qty >  $insertedProduct[$i]['quantity']) {
                $calculated_qty =  $qty - $insertedProduct[$i]['quantity'];

                if (($calculated_qty % $insertedProduct[$i]['quantity']) == 0) {

                    $special_price =  $insertedProduct[$i]['special_price'] + $this->getDiscountedPrice($insertedProduct[$i]['id'], $calculated_qty);
                } else {

                    if ($calculated_qty  >  $insertedProduct[$i]['quantity']) {

                        $special_price =  $insertedProduct[$i]['special_price'] + $this->getDiscountedPrice($insertedProduct[$i]['id'], $calculated_qty);
                    } else {
                        $special_price  = $insertedProduct[$i]['special_price'] + ($calculated_qty *  $insertedProduct[$i]['product_original_price']);
                    }
                }
            }
            //checked in input qty less  than sepcial offer qty 
            if ($qty < $insertedProduct[$i]['quantity']) {
                $special_price = $insertedProduct[$i]['product_original_price'] * $qty;
            }
        }
        return $special_price;
    }


 
}
