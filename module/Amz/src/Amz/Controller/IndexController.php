<?php

namespace Amz\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendService\Amazon\Amazon;
use ZendService\Amazon\Query;
use Amz\Model\Review;
use Zend\Dom\Query as Scrape;


class IndexController extends AbstractActionController{
    
    protected $reviewTable;

    public function indexAction(){
	   // $this->layout()->username = 'adellimore';
        $review = new Review();
        $reviewTable = $this-> getReviewTable();
        //$productTable = $this-> getReviewTable();
        
        //get top 300 products requiring reviews
        
        
       $asins = array('B00006IC3A','B00A29WCA0');
                                
        foreach($asins as $key => $value){
            $itemreview[$key] = $review->grabReview($value);
            $reviewTable -> recordReview($value,$value,$itemreview[$key]['reviewtotal'],$itemreview[$key]['rating']);
        }     
        
        return new ViewModel(array('reviews' => $itemreview, 'asins' => $asins, 'test' => 'test'));
        
    }
    

     protected function getReviewTable(){
        if (!$this->reviewTable) {
            $sm = $this->getServiceLocator();
            $this->reviewTable = $sm->get('Amz\Model\ReviewTable');
            
        }
        return $this->reviewTable;
    }


}

