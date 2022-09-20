<?php
class Controller_Delete extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Delete();
    }

    function action_index()
    {
        $subject = $_POST['delete'];
        $search = '%20';
        $filePath = str_replace($search, " ", $subject) ;
        $this->model->delete($filePath);
        header("Location: /files");
    }

}