<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiAccountController extends AbstractController
{
    private $manager;
    private $validator;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    
    public function __invoke(Request $request, UserPasswordHasherInterface $hasher)
    {

        $data = new User();
        $uploadedFile = $request->files->get('image');
        $data->setUsername($request->request->get('username'));
        $data->setEmail($request->request->get('eMail'));
        $password = $request->get('password');

        if (empty($password) || strlen($password) < 8) {
            $data->setPassword($request->get('password'));
        }else{

            $hash = $hasher->hashPassword($data, $request->get('password'));
            $data->setPassword($hash);
            
        }
        if (!$uploadedFile) {
            return $data;
        } else {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin;Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename . "-" . uniqid() . "." . $uploadedFile->guessExtension();
            $acceptFile = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($uploadedFile->guessExtension(), $acceptFile)) {
                return $data;
            }
            try {
                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                return $e->getMessage();
            }
        }

        $data->setImage($newFilename);

        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {
            return $data;
            
        }

        $this->manager->persist($data);
        $this->manager->flush();

        return $data;
    }

}
