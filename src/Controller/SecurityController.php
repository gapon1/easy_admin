<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function deploy(Request $request): Response
    {
        $headers = getallheaders();
        file_put_contents(dirname(__DIR__, 2).'/var/log/headers.log', print_r($headers, true));

        $secret = 'crypto_deploy'; // Replace with your GitHub webhook secret

        $signatureHeader = $headers['X-Hub-Signature-256'] ?? null; // Fetch the header properly

        if ($signatureHeader === null) {
            return new Response('Missing X-Hub-Signature-256 header', 400);
        }

        $payload = $request->getContent();
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($signature, $signatureHeader)) {
            return new Response('Invalid signature', 403);
        }

        $output = [];
        exec('cd /home/u538818725/domains/growupcrypto.site/public_html && git pull 2>&1', $output);

        return new Response(implode("\n", $output), 200);
    }
}
