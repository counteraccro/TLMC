<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportManager extends AppService
{
    /**
     * Fonction qui génère au format CSV un fichier téléchargeable
     * il s'agit d'une fonction générique à laquelle on peut passer des informations différentes
     * ex : génération d'un fichier CSV permettant l'export de questions, et/ou de réponses à un questionnaire.
     *
     * @param string $fileName
     * @param array $fileContent
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateCSV($fileName, $arrayContent = array())
    {
        $fileName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $fileName);
        $fileName = str_replace(array(
            "'",
            ' '
        ), array(
            '',
            '-'
        ), $fileName);

        $fileContent = '';
        $tabKey = array_keys($arrayContent);
        foreach ($arrayContent as $entete) {
            $tabKey = array_keys($entete);
            foreach ($tabKey as $key) {
                $fileContent .= $key . ';';
            }
            break;
        }
        $fileContent = substr($fileContent, 0, - 1) . "\n";

        foreach ($arrayContent as $content) {
            foreach ($content as $data) {
                $fileContent .= $data . ';';
            }
            $fileContent = substr($fileContent, 0, - 1) . "\n";
        }

        $response = new Response(utf8_decode($fileContent));
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName . '.csv');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}