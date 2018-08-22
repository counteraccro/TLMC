<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ExportManager extends AppService
{

    /**
     * Fonction qui génère au format CSV un fichier téléchargeable
     * il s'agit d'une fonction générique à laquelle on peut passer des informations différentes
     * ex : génération d'un fichier CSV permettant l'export de questions, et/ou de réponses à un questionnaire.
     *
     * @param string $fileName
     * @param array $arrayContent
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

        $fileContent = str_replace(array(
            '\\?',
            '\\',
            'd?',
            'l?',
            'c?',
            'j?',
            'm?',
            'n?',
            't?'
        ), array(
            "'",
            "'",
            "d'",
            "l'",
            "c'",
            "j'",
            "m'",
            "n'",
            "t'"
        ), utf8_decode($fileContent));
        $response = new Response($fileContent);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName . '.csv');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    public function generateXML($fileName, $object, $attributes = array(), $arrayCallback = array())
    {
        $fileName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $fileName);
        $fileName = str_replace(array(
            "'",
            ' '
        ), array(
            '',
            '-'
        ), $fileName);

       

        $normalizer = new ObjectNormalizer();
        $normalizer->setCallbacks($arrayCallback);

        $encoders = array(
            new XmlEncoder(),
            new JsonEncoder()
        );
        $normalizers = array(
            $normalizer,
            new DateTimeNormalizer()
        );

        $serializer = new Serializer($normalizers, $encoders);

        $arrayData = $serializer->normalize($object, null, array(
            'attributes' => $attributes,
            DateTimeNormalizer::FORMAT_KEY => 'Y/m'
        ));

        $arrayToXml = array(
            strtolower(str_replace('App\Entity\\', '', get_class($object))) => $arrayData
        );

        $xml = $serializer->serialize($arrayToXml, 'xml');

        $response = new Response($xml);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName . '.xml');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}