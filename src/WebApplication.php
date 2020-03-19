<?php


namespace Tudublin;


class WebApplication
{
    public function run()
    {
        $action = filter_input(INPUT_GET, 'action');
        $mainController = new MainController();

        switch ($action) {
            case 'about':
                $mainController->about();
                break;

            case 'contact':
                $mainController->contact();
                break;

            case 'list':
                $mainController->listMovies();
                break;

            case 'sitemap':
                $mainController->sitemap();
                break;

            default:
                $mainController->home();
        }
    }
}