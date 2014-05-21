<?php class Report extends Core_controller
{

    public function __construct()
    {
        parent::__construct('admin');

        $this->template->setPartial('navbar')
            ->setPartial('headermeta')
            ->setPartial('footer')
            ->setPartial('flashmessage');
        
        $this->lists_m = Load::model('lists_m');
        $this->page_m  = Load::model('page_m');
        $this->questions_m = Load::model('questions_m');
        $this->answers_m = Load::model('answers_m');
        $this->langs_m = Load::model('langs_m');
        $this->data_m = Load::model('data_m');
        
        $this->menu_m = Load::model('menu_m');
        $this->template->menuitems = $this->menu_m->getBeheerderMenu($this->lang);
        $this->template->langs = $this->langs_m->getLangs();
		
	$this->template->setPagetitle('Bruggman');
    }
    
    function checkPrivilege()
    {
        if (!isset($_SESSION['admin'])){
            unset($_SESSION['user']);
            $this->setFlashmessage($this->lang['accessdenied'], 'danger');
            $this->redirect('home/index');
            return false;
        } else {
            return true;
        }
    }
    
    function generateRandomString($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
    
    function reArrayFiles(&$file_post) {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    
    public function downloadFile($file)
    {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
    }
    
    public function index()
    {
        if ($this->checkPrivilege() == true){
            $this->setFlashmessage($this->lang['accessdenied'], 'danger');
            $this->redirect('admin/index');
        }
    }
    
    
    public function generate($userid=false, $language=false)
    {
        if ($this->checkPrivilege()){
            if ($userid){
                if ($_POST){
                    if (isset($_FILES['files'])){                
                        $files = $this->reArrayFiles($_FILES['files']);
                        $allowedExts = array("html", "txt", "rtf");

                        $ok = true;
                        $error = 0;
                        foreach ($files as $file){
                            $temp = explode(".", $file["name"]);
                            $ext = end($temp);
                            if (!in_array($ext, $allowedExts) or $file['error'] > 0){
                                $ok = false;
                                $error = $file['error'];
                            }
                        }

                        if ($ok) {
                            foreach ($files as $file){
                                $datafile = "upload/" . $this->generateRandomString() . '.' .end(explode('.', $file['name']));
                                move_uploaded_file($_FILES["file"]["tmp_name"], $datafile);
                                $datastr .= $datafile . ',';
                            }
                            $datastr = rtrim($datastr, ',');
                            if (!$language){
                                $language = 'nl';
                            }
                            $results = $this->generateRandomString(5) . '.docx';
                            exec("scripts/readout_data.py --language $language --output $results $datastr", $output, $return_var);
                            
                            if ($return_var != 0){
                                $this->setFlashmessage($this->lang['scripterror'] . ' (' . $return_var . ')', 'danger');
                                $this->redirect('report/generate/' . $userid . '/' . $lang);
                            } else {
                                $this->downloadFile($results);
                                $this->template->output = $output;
                                $this->template->render('report/result');
                            }
                        } else {
                            $this->setCurrentFlashmessage('Not OK: ' . $this->lang['invalidfile'] . ' (' . $error . ')', 'danger');
                            $this->template->userid= $userid;
                            $this->template->langcode = $language;
                            $this->template->render('report/upload'); 
                        }
                    } else {
                            $this->setCurrentFlashmessage($this->lang['invalidfile'] . ' (' . $error . ')', 'danger');
                            $this->template->userid= $userid;
                            $this->template->langcode = $language;
                            $this->template->render('report/upload');
                    }
                } else {
                    //upload it
                    $this->template->userid= $userid;
                    $this->template->langcode = $language;
                    $this->template->render('report/upload');
                } 
            } else {
                $this->setCurrentFlashmessage($this->lang['noparameter'], 'danger');
                $this->redirect('admin/index');
            }
        }
    }
   
}