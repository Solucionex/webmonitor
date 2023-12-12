<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer
    )
    {
        
    }
    #[Route('/endpoint/notification', name: 'app_notification')]
    public function index(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $sensor = $data['data'][0]['id'];
        $status = $data['data'][0]['status']['value'];
        $date = (new DateTime($data['data'][0]['status']['metadata']['TimeInstant']['value'], new DateTimeZone('Europe/Madrid')))->format('Y-m-d H:i:s');
        if($status == 0){
            $email = (new Email())
                ->from('no-reply@webmonitor.solucionex.dev') 
                ->to('manuel.aguilar@gmail.com') // Cambiar por el correo electrónico del currentUser
                ->subject('[WebMonitor] Host is down!')
                ->text("$sensor is down since $date");
            $this->mailer->send($email);
        }
        return new Response('Notificación recibida', Response::HTTP_OK);
    }
}
