<?php


namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class test
{

    /**
     * @Route("/hello/{prenom?anonyme}", name="hello" )
     */

    public function Hello($prenom)
    {

        return new Response("<h1>Hello $prenom</h1>");
    }
}
