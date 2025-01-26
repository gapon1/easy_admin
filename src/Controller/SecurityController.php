<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/deploy', name: 'app_deploy')]
    public function deploy()
    {

        $secret = 'crypto_deploy'; // Replace with your GitHub webhook secret

    // Verify the request signature
        $signature = 'sha256=' . hash_hmac('sha256', file_get_contents('php://input'), $secret);

        $signatureHeader = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? null;
        if ($signatureHeader === null) {
            http_response_code(400);
            echo 'Missing HTTP_X_HUB_SIGNATURE_256 header';
            exit;
        }

    // Execute Git pull
        $output = [];
        exec('cd /home/u538818725/domains/growupcrypto.site/public_html && git pull 2>&1', $output);
        echo implode("\n", $output);


        //return new Response($message);
    }
}
