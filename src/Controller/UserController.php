<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController {
    /**
     * User Login
     *
     * @FOSRest\Post("/api/auth")
     *
     * @param $request Request
     * @param $userRepository UserRepository
     * @param $encoder UserPasswordEncoderInterface
     * @param $jwt JWTEncoderInterface
     *
     * @return View
     * @throws
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder, JWTEncoderInterface $jwt) {
        $data = json_decode($request->getContent());

        $user = $userRepository->findOneBy(['username' => $data->username]);
        if(!$user) {
            return View::create(null, Response::HTTP_NOT_FOUND);
        }

        if(!$encoder->isPasswordValid($user, $data->password)) {
            return view::create("bad pass", Response::HTTP_NOT_FOUND);
        }

        $token = $jwt->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 60 // 1 hour expiration
        ]);

        return View::create($token, Response::HTTP_OK);
    }
}
