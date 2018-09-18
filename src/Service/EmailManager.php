<?php
namespace App\Service;

class EmailManager extends AppService
{
    /**
     * Object mailer
     *
     * @var \Swift_Mailer
     */
    private $swift_Mailer;
    
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->swift_Mailer = $mailer;
    }
    
    public function send(array $params)
    {
        echo "oki";
    }
//     public function send($name, \Swift_Mailer $mailer)
//     {
//         $message = (new \Swift_Message('Hello Email'))
//             ->setFrom('send@example.com')
//             ->setTo('recipient@example.com')
//             ->setBody(
//                 $this->renderView(
//                     'emails/registration.html.twig',
//                     array('name' => $name)
//                     ),
//                 'text/html'
//                 );

//         $mailer->send($message);
//     }
}