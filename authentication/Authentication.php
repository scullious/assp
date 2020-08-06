<?php
/************************************************************************************************************************
*************************************************************************************************************************
//  Класс для сквозной авторизации
//  
//  поля AD, которые можно применить в фильтре (из тех что понятны):
//  displayname - ФИО
//  memberof - группы пользователя
//  department - подразделение пользователя
//  directreports - подчинённые
//  samaccountname - логин
//  mail - email
//  manager - руководитель
//  mobile - мобильный
//  title - должность
//  telephonenumber - номер телефона (короткий)
//  
//  не самые востребованные, но тоже понятные поля:
//  cn - ФИО
//  sn - фамилия
//  givenname - имя отчество
//  distinguishedname - поле AD с OU. Например CN=Созинов Антон Васильевич,OU=1.Clients,DC=spb,DC=rsvo,DC=local
//  whencreated - вероятно дата создания
//  whenchanged - вероятно дата последних изменений
//  company - компания
//  streetaddress - адрес
//  name - ФИО
//  lastlogon - вероятно дата последнего входа
//  userprincipalname - полное доменное имя
//  dn - поле AD с OU. Например CN=Созинов Антон Васильевич,OU=1.Clients,DC=spb,DC=rsvo,DC=local
//  
*************************************************************************************************************************
*************************************************************************************************************************/
    class Authentication {
        private $LDAPServer = 'spb.rsvo.local';
        private $OU          = 'OU=1.Clients,DC=spb,DC=rsvo,DC=local';
        private $remoteUserTest = 'a_sozinov';
        private $filter     = 'samaccountname=';
        private $domain     = '@spb.rsvo.local';
        private $debugIP    = '';
        private $ldap       = '';
        private $userName   = '';
        private $remote_user = '';

        //данные из AD
        private $displayname     = ''; //ФИО
        private $memberof        = ''; //группы пользователя
        private $department      = ''; //подразделение пользователя
        private $directreports   = ''; //подчинённые
        private $samaccountname  = ''; //логин
        private $email           = ''; //email
        private $manager         = ''; //руководитель
        private $mobile          = ''; //мобильный
        private $title           = ''; //должность
        private $telephonenumber = ''; //номер телефона (короткий)

        //конструктор класса. Получение данных из AD
        public function __construct($virtualUser = ''){
            //если не существует поля REMOTE_USER, то блокируем общение с классом.
            if($virtualUser != ''){
                $_SERVER['REMOTE_USER'] = $virtualUser;
            }
            if(!isset($_SERVER['REMOTE_USER'])){
                //echo 'Объект не может быть создан, т.к. отсутствуют данные в $_SERVER[\'REMOTE_USER\']';
                echo $this->showMessage('Обнаружена проблема на сервере. Обратитесь к администратору.','oops.png');
                error_log('Object cannot be created because missing data in $_SERVER [\'REMOTE_USER \'] ');
            } else {
                $this->remote_user = $_SERVER['REMOTE_USER'];
                $infoAD = $this->getInfoAD(['displayname','memberof','department','directreports','samaccountname','mail','manager','mobile','title','telephonenumber']);
                if($infoAD){
                    if(isset($infoAD[0]['displayname'][0])){
                        $this->displayname     = $infoAD[0]['displayname'][0];
                    }
                    if(isset($infoAD[0]['memberof'])){
                        $this->memberof        = $infoAD[0]['memberof'];
                    }
                    if(isset($infoAD[0]['department'][0])){
                        $this->department      = $infoAD[0]['department'][0];
                    }
                    if(isset($infoAD[0]['directreports'])){
                        $this->directreports   = $infoAD[0]['directreports'];
                    }
                    if(isset($infoAD[0]['samaccountname'][0])){
                        $this->samaccountname  = $infoAD[0]['samaccountname'][0];
                    }
                    if(isset($infoAD[0]['mail'][0])){
                        $this->email           = $infoAD[0]['mail'][0];
                    }
                    if(isset($infoAD[0]['manager'][0])){
                        $this->manager         = $infoAD[0]['manager'][0];
                    }
                    if(isset($infoAD[0]['mobile'][0])){
                        $this->mobile          = $infoAD[0]['mobile'][0];
                    }
                    if(isset($infoAD[0]['title'][0])){
                        $this->title           = $infoAD[0]['title'][0];
                    }
                    if(isset($infoAD[0]['telephonenumber'][0])){
                        $this->telephonenumber = $infoAD[0]['telephonenumber'][0];
                    }
                } else {
                    //echo 'Неудалось получить данные из AD';
                    echo $this->showMessage('Обнаружена проблема на сервере. Обратитесь к администратору.','oops.png');
                    error_log('Failed to get data from AD');
                }
            }
        }

        //подключение к AD
        private function connectLDAP($remoteUser){
            if($this->debugIP == ''){
                $this->userName = $this->getUserName($remoteUser);
            }

            //подключаемся к LDAP
            $this->ldap = ldap_connect($this->LDAPServer) or die("Невозможно соединиться с " . $this->LDAPServer . " ");

            //проверка прохождения аутентификации
            if (($remoteUser != $this->remoteUserTest) and (!isset($_SERVER['PHP_AUTH_USER']))) {
                return false;
            }

            //Привязка к LDAP директории
            if (isset($_SERVER['PHP_AUTH_PW'])) {
                @$bind = ldap_bind($this->ldap,$this->userName.$this->domain,$_SERVER['PHP_AUTH_PW']);
            }
            else {
                @$bind = ldap_bind($this->ldap,'Ldap'.$this->domain,'pdE~~s@lXNwY}G~');
            }
            return $bind;
        }

        //проверка получения remote_user'а
        private function checkRemoteUser(){
            if ($this->remote_user == ''){
                return false;
            } else {
                return true;
            }
        }

        //получение логина пользователя
        private function getUserName($userName){
            //if(strpos('\\', $userName)<>0){
                //$u = str_replace('\\',' ', $userName);
            $arr_username = explode('\\', $userName);
            if (isset($arr_username[1])){
                return $arr_username[1];
            } else {
               return $userName;
            }
        }

        //конвертация кодировки массива
        private function encodeArray($arr) {
            foreach ($arr as $n => $v) {
                if (is_array($v)) {
                    $arr[$n] = $this->encodeArray($v);
                } else {
                    $arr[$n] = mb_convert_encoding($v, 'UTF-8', 'cp1251');
                }
            }
            return $arr;
        }

        //посмотреть $_SERVER
        public function viewServerArray(){
            echo '<pre>';
            print_r($_SERVER);
            echo '</pre>';
        }

        //получение полной инфорации из AD (по умолчанию выгружается вся доступная информация)
        private function getInfoAD($justthese = array("*")){
            if($this->checkRemoteUser()){
                $result = false;
                if ($this->connectLDAP($this->remote_user)){
                    $result = ldap_search($this->ldap,$this->OU,"(".$this->filter.$this->userName.")",$justthese);
                    if ($result) {
                        // Получаем все записи результата
                        $info = ldap_get_entries($this->ldap,$result);
                        $result = $this->encodeArray($info);
                    }
                    ldap_close($this->ldap);
                }
                return $result;
            }
        }

        //сообщение для пользователя при некорректной авторизации
        private function showMessage($message, $image){
            $width = strlen($message) + 200;
            $html = '<!DOCTYPE html>
                    <html>
                        <head>
                            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                            <title></title>
                        </head>
                        <body>
                            <div style="width: 100%; height: 100%; position: absolute; top: 0; left: 0;">
                                <div style="width: ' . $width . 'px; height: 250px; position: absolute; top: 0; right: 0; bottom: 0; left: 0; margin: auto;" align="center">
                                    <font style="font-size:16pt;">' . $message . '</font>
                                </div>
                                <div style="width: 250px; height: 250px; position: absolute; top: 95px; right: 0; bottom: 0; left: 0; margin: auto;" align="center">
                                    <img src="authentication/img/' . $image . '">
                                </div>
                            </div>
                        </body>';
             return $html;
        }

        //установка ip адреса, которому будет открыт доступ к странице
        public function setDebugMode($ip, $mode){
            if($mode){
                $this->debugIP = $ip;
                return true;
            }
            else {
                $this->debugIP = '';
            }
            return false;
        }

        //проверка принадлежности пользователя группе
        public function checkValidGroup($search_group){
            if($this->checkRemoteUser()){
                //если данные по группам пользователя не пустые, то пробуем найти соответствие
                if($this->memberof != ''){
                    foreach($this->memberof as $group){
                        if(strripos($group, $search_group) <> 0){
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        //проверка по ФИО
        public function checkValidFIO($search_fio){
            if($this->checkRemoteUser()){
                if($this->displayname == $search_fio){
                    return true;
                }
            }
            return false;
        }

        //проверка доступа пользователя по списку групп/или одной группе
        public function checkAccessUserByGroup($group_array){
            
            if($this->checkRemoteUser()){
                if($this->debugIP == ''){
                    //если данные по группам пользователя не пустые, то пробуем найти соответствие
                    if($this->memberof != ''){
                        $result = false;
                        if(is_array($group_array)){
                            //по умолчанию считаем, что пользователь должен состоять только в одной группе из поступившего списка
                            //какую первую нашли, ту и возвращаем
                            foreach($group_array as $group){
                                if($this->checkValidGroup($group)){
                                    return $group;
                                }
                            }
                            
                        } else {
                            if($this->checkValidGroup($group_array)){
                                return $group_array;
                            }
                        }
                    }
                } else {
                    if ($_SERVER['REMOTE_ADDR'] <> $this->debugIP){
                        echo $this->showMessage('НА САЙТЕ ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ!','techworkers.png');
                        return false;
                    } else {
                        return true;
                    }
                }
            }
            echo $this->showMessage('К сожалению вам доступ закрыт!','stop.png');
            return false;
        }




        //получить ФИО
        public function getFIO(){
            if($this->checkRemoteUser()){
                return $this->displayname;
            } else {
                return false;
            }
        }

        //получить группы пользователя
        public function getGroups(){
            if($this->checkRemoteUser()){
                //отфильтровываем только названия групп
                foreach($this->memberof as $group){
                    $tmp = explode(',',$group);
                    $result[] = substr($tmp[0],3);
                }
                return $result;
            } else {
                return false;
            }
        }

        //получить подразделение пользователя
        public function getDepartment(){
            if($this->checkRemoteUser()){
                return $this->department;
            } else {
                return false;
            }
        }

        //получить подчинённые
        public function getSubordinate(){
            if($this->checkRemoteUser()){
                //отфильтровываем OU только по Clients
                foreach($this->directreports as $client){
                    if(stripos($client,'OU=1.Clients') <> 0){
                        $tmp = explode(',',$client);
                        $result[] = substr($tmp[0],3);
                    }
                }
                return $result;
            } else {
                return false;
            }
        }

        //получить логин
        public function getLogin(){
            if($this->checkRemoteUser()){
                return $this->samaccountname;
            } else {
                return false;
            }
        }

        //получить email
        public function getEmail(){
            if($this->checkRemoteUser()){
                return $this->email;
            } else {
                return false;
            }
        }

        //получить руководитель
        public function getLeader(){
            if($this->checkRemoteUser()){
                $tmp = explode(',',$this->manager);
                $result = substr($tmp[0],3);
                return $result;
            } else {
                return false;
            }
        }

        //получить мобильный
        public function getMobile(){
            if($this->checkRemoteUser()){
                return $this->mobile;
            } else {
                return false;
            }
        }

        //получить должность
        public function getPosition(){
            if($this->checkRemoteUser()){
                return $this->title;
            } else {
                return false;
            }
        }

        //получить номер телефона (короткий)
        public function getShortPhone(){
            if($this->checkRemoteUser()){
                return $this->telephonenumber;
            } else {
                return false;
            }
        }

    }
?>