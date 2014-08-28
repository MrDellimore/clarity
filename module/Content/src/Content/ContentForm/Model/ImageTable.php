<?php

namespace Content\ContentForm\Products\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;
use Content\ContentForm\Products\Entity\Images;



class ImageTable{

    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function saveImageFile($data){

        /*
         * todo fix validation code to see that file is an image
         */


        $filename = $data['files'][0]['name'];
        $filetmp =$data['files'][0]['tmp_name'];
        $filetype =$data['files'][0]['type'];
        $filesize =$data['files'][0]['size'];
        //regex expression check to grab filetype
        preg_match('/.+\/(.+)/', $filetype, $matches);
        $extension = $matches[1];

        //create unique filename
        $newFilename = sprintf('%s.%s', sha1(uniqid(time(), true)), $extension);

        $success = move_uploaded_file($filetmp, 'public/images/'.$newFilename);

        if($success == true){

            $message = array('files'=>
                array(array(
                    'name'        => $filename,
                    'size'          => $filesize,
                    'url'           => '/images/'.$newFilename,
                    'thumbnailurl'  => '/images/'.$newFilename,
                    'deleteURL'     => '/images/'.$newFilename,
                    'deleteType'    => 'DELETE')));
        }
        else{
            $message = array('files'=>
                array(array(
                    'name'        => $filename,
                    'size'          => $filesize,
                    'url'           => '/images/'.$newFilename,
                    'thumbnailurl'  => '/images/'.$newFilename,
                    'deleteURL'     => '/images/'.$newFilename,
                    'deleteType'    => 'DELETE')));
        }


        return $message;
    }

    public function createImage(Images $image,$entityid){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $insert = $this->sql->insert('productattribute_images');
        $insert->columns(array('entity_id','label','position','disabled','domain','filename','default','datastate','changedby'));
        $insert->values(array(
            'entity_id' => $entityid,
            'label' => $image->getLabel(),
            'position' => $image->getPosition(),
            'disabled' => 0,
            'domain' => '',
            'filename' => $image->getFilename(),
            'default' => is_null($image->getDefault())?0:1,
            'datastate' => 2,
            'changedby' => $user
        ));

        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        return $image->getLabel() ." has been uploaded";

    }

    public function updateImage(Images $image){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $setArray= array();
        $message = '';

        //check that each feild updating is not null
        if(!(is_null($image->getLabel()))){
            $setArray['label'] = $image->getLabel();
        }
        if(!(is_null($image->getPosition()))){
            $setArray['position'] = $image->getPosition();
        }
        if(!(is_null($image->getDisabled()))){
            $setArray['disabled'] = $image->getDisabled();
        }
        if(!(is_null($image->getDisabled()))){
            $setArray['default'] = $image->getDefault();
        }

        if((is_null($image->getId()))){
            unset($setArray);
        }



        if(!empty($setArray)){
            $setArray['datastate'] = 1;
            $setArray['changedby'] = $user;

            $update = $this->sql->update('productattribute_images');
            $update->set($setArray);
            $update->where(array('value_id' => $image->getId() ));
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();

            $message .= $image->getLabel() ." has been updated <br />";
        }

        return $message;
    }

}