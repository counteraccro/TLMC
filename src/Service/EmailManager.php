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

    private $params = array(
        'expediteur' => 'admin@tlmc.com',
        'destinataire' => '',
        'body' => '',
        'type' => 'text/html'
    );

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->swift_Mailer = $mailer;
    }

    /**
     * Fonction permettant l'envoi d'un mail en automatique
     * Prend en paramètres un tableau comprenant le destinataire, l'expéditeur et le corps du message
     * @param array $params
     */
    public function send(array $params)
    {
        $this->validateParameter($params);
        
        $message = (new \Swift_Message('Plateforme TLMC'))->setFrom($this->params['expediteur'])
            ->setTo($this->params['destinataire'])
            ->setBody($this->params['body'], $this->params['type']);
        $this->swift_Mailer->send($message);
    }
    
    /**
     * Valide les paramètres qui serviront plus tard à l'envoi de mail en automatique (vérification clés-valeurs)
     * @param array $params_
     * @throws \ErrorException
     */
    private function validateParameter(array $params_)
    {
        foreach($this->params as $k => $v)
        {
            if(!isset($params_[$k]))
            {
                if($v == '')
                {
                    throw new \ErrorException("Le parametre ' . $k . ' est manquant");
                }
            }
            else 
            {
                $this->params[$k] = $params_[$k];  
            }
        }
    }
}