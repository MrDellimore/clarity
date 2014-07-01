<?php
namespace Amz\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;

class ReviewTable extends AbstractTableGateway implements AdapterAwareInterface{
    protected $table = 'reviews';
    const TABLE_NAME = 'reviews';
    
    public function setDbAdapter(Adapter $adapter){
        $this->adapter = $adapter;
        $this->initialize();
    }
    

    public function getSkus($sku){
        $select = $this->sql->select()->where(array('sku' => $sku))->order('reviewDate DESC');
        return $this->selectWith($select);
    }
    
    
    public function recordReview($sku, $asin, $reviewCount, $rating){
        if($this->getSkus($sku)->count() == 0){ 
            $this->insertReview($sku,$asin,$reviewCount,$rating);
        }
        else{
            $this->updateReview($sku,$asin,$reviewCount,$rating);
        }
    }
    
    
    
    public function insertReview($sku, $asin, $reviewCount, $rating){
        return $this->insert(array(
                'sku' => $sku,
                'asin' => $asin,
                'reviewCount' => $reviewCount,
                'rating' => $rating,
                'reviewDate' => new Expression('NOW()')));
        
    }
    
    public function updateReview($sku, $asin, $reviewCount, $rating){
        return $this->update(array(
                'sku' => $sku,
                'asin' => $asin,
                'reviewCount' => $reviewCount,
                'rating' => $rating,
                'reviewDate' => new Expression('NOW()')), array('sku' => $sku)); //-> where(array('sku' => $sku));
        
    }
    
    
    /*
     * Get items based on rating
     * Filters are a search parameter by range,greater than, less than
     */
    public function getReviewsbyRating($rating, $filter){
        
    } 
    
    
    /*
     * Get Items based on review count
     * Filters are search parameters by range,greater than, less than
     */
     
     public function getReviewsbyReviewCount($count, $filter){
         
     }
    
}