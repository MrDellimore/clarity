<?php
namespace Amz\Model;

use Zend\Dom\Query as Scrape;
use ZendService\Amazon\Amazon;





class Review{
    
	protected $reviewTable;
    
    public function grabReview($asin) {
        
        $amazon = new Amazon('AKIAIOMOVY4YIBKUQSTA', 'US', 'bNOf2/z2+fjtqZSXBW1BR6TSuzFkzQkPERiz3aEL');
        //$amazon2 = new Query('AKIAJA5BMDE3CCXUPQHQ', 'US', 'vSDFdmOsl06lqf6he/a08/OT89NATbolv531ZCDM');
                                     
        $item=$amazon->itemLookup($asin);
        $xmlResponse = $item->asXml();
            
        
        $res = new Scrape($xmlResponse);
        $urlReview = $res->execute('iframeurl')->current()->nodeValue;
        $hasReview = $res->execute('hasreviews')->current()->nodeValue;
        
        if($hasReview == "true"){
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $urlReview); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $html = curl_exec($ch); 
            curl_close($ch);   
            
            $dom = new Scrape($html);
            $numReviews = $dom->execute('div .crIFrameHeaderHistogram b')->current()->nodeValue;
            $numReviews = str_replace('Review','',str_replace('Reviews','',$numReviews));
            $star = $dom->execute('div .crIFrameHeaderHistogram table')->current()->nodeValue;
            

            preg_match_all('/\([0-9]*\)/',$star,$match);
            $starReviews = str_replace('(','',str_replace(')','',$match[0]));
            
            //multiply array (key* value)
            $i=5;
            foreach($starReviews as $key =>$value){
                $starReviews[$key] = $i * $value;
                $i--;
            }
            
            //sum array
            $starSum = array_sum($starReviews);
            
            //totalreviews / arraysum = rating
            $rating = number_format($starSum/$numReviews,1);
        }
            
        else{
            $numReviews = 0;
            $rating = 0;
        }
       
        $result = array ('reviewtotal' => $numReviews, 'rating' => $rating);
        return  $result;
            
    }// end grab




}//end class
