<?php
namespace App\Controllers;
use App\Core\Controller;
class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);

        $this->render('errors/404', [
            'pageTitle' => 'Сторінку не знайдено'
        ]);
    }
}
