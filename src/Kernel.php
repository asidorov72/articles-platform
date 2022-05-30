<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

//    public function registerBundles()
//    {
//        return array(
//            // Предыдущий код
////            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
//            // Последующий код
//        );
//    }
}