<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommonFunctions extends AbstractController
{
    public function storeImage($image, SluggerInterface $slugger, ImageOptimizer $imageOptimizer, bool $isPost): string
    {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
        if($isPost){
            $parameter = 'post_images_directory';
        }
        else{
            $parameter='user_images_directory';
        }

        try {
            $image->move(
                $this->getParameter($parameter),
                $newFilename
            );
            $imageOptimizer->resize($this->getParameter($parameter) . '/' .$newFilename);
            return $newFilename;
        } catch (FileException $e) {
            throw new \HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occurred while uploading a image.');
        }
    }
}