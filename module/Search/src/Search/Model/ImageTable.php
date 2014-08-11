<?php

namespace Search\Model;




class ImageTable{

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

}