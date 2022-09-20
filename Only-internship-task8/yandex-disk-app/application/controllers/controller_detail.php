<?php
class Controller_Detail extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Detail();
    }

    function action_index()
    {
        $subject = $_POST['detail'];
        $search = '%20';
        $filePath = str_replace($search, " ", $subject) ;
        $data = $this->model->readDetail($filePath);
        $this->view->generate('detail_view.php', 'template_view.php', $data);
    }

}