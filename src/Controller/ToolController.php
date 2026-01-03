<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools', name: 'app_tool_')]
class ToolController extends AbstractController
{
    #[Route('/cross-product', name: 'cross_product')]
    public function crossProduct(): Response
    {
        return $this->render('tool/cross_product.html.twig');
    }
}
