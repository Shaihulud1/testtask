<?php
namespace app; 

abstract class AbstractController
{
    protected function renderView(string $requestView, array $vars = []): void
    {
        $requestView .= ".php";
        $views = scandir(__DIR__.'/views');
        foreach ($views as $view) {
            if ($view == $requestView) {
                if (!empty($vars)) {
                    extract($vars, EXTR_PREFIX_IF_EXISTS, 'app');
                }
                require_once 'views/'.$view;
                break;
            }
        }
    }
    
    protected function renderJson(array $ar2Json): void
    {
        header('Content-Type: application/json');
        print_R(json_encode($ar2Json));
    }
}