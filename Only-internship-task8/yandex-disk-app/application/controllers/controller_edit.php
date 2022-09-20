<?php
class Controller_Edit extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Edit();
    }

    function action_index()
    {
        $subject = $_POST['edit'];
        $search = '%20';
        $filePath = str_replace($search, " ", $subject) ;
        $data = $this->model->readDetail($filePath);
        $this->view->generate('edit_view.php', 'template_view.php', $data);
    }

}
