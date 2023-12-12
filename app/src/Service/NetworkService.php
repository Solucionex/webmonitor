<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class NetworkService
{
    public function ping($host, $port, $timeout = 10): Response
    {
        // Intentar abrir una conexión
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if (!$fp) {
            // No se pudo establecer conexión
            $httpStatus = Response::HTTP_BAD_REQUEST;
        } else {
            // Conexión exitosa
            $httpStatus = Response::HTTP_OK;
            fclose($fp);
        }

        return new Response('',$httpStatus);
    }
}