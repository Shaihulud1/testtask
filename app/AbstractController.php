<?php
namespace app; 

abstract class AbstractController
{
    protected function renderView(string $requestView, array $vars = []): void
    {
        $requestView .= ".php";
        $views = scandir($_SERVER['DOCUMENT_ROOT'].'/app/views');
        foreach ($views as $view) {
            if ($view == $requestView) {
                if (!empty($vars)) {
                    extract($vars, EXTR_PREFIX_SAME, 'app');
                }
                require_once $_SERVER['DOCUMENT_ROOT'].'/app/views/'.$view;
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