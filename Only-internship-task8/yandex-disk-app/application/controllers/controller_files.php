<?php

class Controller_Files extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Files();
    }

    function action_index()
    {
        $data = $this->model->getDataArr();
        $this->view->generate('files_view.php', 'template_view.php', $data);
    }

}
