<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Handler;

use Illuminate\Foundation\Exceptions\Handler as LaravelExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Pandawa\Component\Foundation\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ExceptionHandler extends LaravelExceptionHandler
{
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if (!$this->router()->getRoutes()->count()) {
                return $this->createWelcomeResponse();
            }

            return null;
        });
    }

    protected function getHttpExceptionView(HttpExceptionInterface $e): ?string
    {
        return null;
    }

    protected function registerErrorViewPaths(): void
    {
    }

    protected function router(): Router
    {
        return $this->container->get('router');
    }

    protected function createWelcomeResponse(): Response
    {
        $version = substr(Application::VERSION, 0, 3);

        ob_start();
        include dirname(__DIR__).'/Resources/views/welcome.html.php';

        return new Response(ob_get_clean(), Response::HTTP_NOT_FOUND);
    }
}
