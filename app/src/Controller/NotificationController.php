<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Security $security
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
        $target = $this->security->getUser()->getEmail();
        if($status == 0 && $target != null){
            $email = (new Email())
                ->from('no-reply@webmonitor.solucionex.dev') 
                ->to($target)
                ->subject('[WebMonitor] Host is down!')
                ->text("$sensor is down since $date");
            $this->mailer->send($email);
        }
        return new Response('Notificación recibida', Response::HTTP_OK);
    }
}
