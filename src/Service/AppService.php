<?php
namespace App\Service;

class AppService {
        
    /**
     * Debug array
     *
     * @param mixed $data
     */
    protected function pre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}