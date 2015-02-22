<?php
include_once 'settings.php';
require_once 'common.db.php';
//require_once $GLOBALS['DocumentRoot'] . '/component/TimeDiffCalculator/time.diff.calculator.ui.php';

class CommonCode {

    private $_setting;

    public function __construct() {

        $this->_setting = new Settings();
        $this->_dbobj = new Common_db();
       // $this->_Time = new TimeDiffCalculator();
    }
    
    public function GetUserInfo($UserID)
    {
       $result=''; 
       $User_Info = array();
       $User_Info = $this->_dbobj->GetUSerBaicInformation($UserID);  
       if(count($User_Info)>0)
       {
          $result = $User_Info;
       }    
       return $result;
    }
    
    public function MyPageURL()
    {
        $_SESSION['LastVisitedPage'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $_SESSION['LastVisitedPage'];
    }

    public function IsUserLogin()
    {
       if((!isset($_SESSION['UserId'])) || ($_SESSION['UserId'] == "")|| ($_SESSION['UserId'] == 0)) {
            $this->MyPageURL();
            $redirect = $this->GetSEOFriendlyURL('home', '');       
            header('location:' . $redirect);
        }
    }
    
    public function MakeUserDataContainer($userid)
    {
        $RootURL = $this->_setting->GetProjectPath();
        $PathToMainDirectory = $RootURL . "resource/users/";
        $pathtonew = $userid . "/";
        $MKDR= mkdir("$PathToMainDirectory$pathtonew",0777);
        if ($MKDR) {
            chmod("$PathToMainDirectory$pathtonew", 0777);
            $PathToMainDirectory = $RootURL . "resource/users/" . $userid . "/";
            $pathtonew = "ProfileImages/";
             $MKDR_2 = mkdir($PathToMainDirectory . $pathtonew,0777);
             chmod("$PathToMainDirectory$pathtonew", 0777);
            if ($MKDR_2) {
                $result = true;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    public function MakeLessonsContainer($userid)
    {

        $RootURL = $this->_setting->GetProjectPath();
        $PathToMainDirectory = $RootURL . "resource/users/" . $userid . "/";
        $pathtonew = "listings/";
        if (mkdir($PathToMainDirectory . $pathtonew)) {
            $PathToMainDirectory = $RootURL . "resource/users/" . $userid . "/listings/";
            $pathtonew = "lessons/";
            if (mkdir($PathToMainDirectory . $pathtonew)) {
                $PathToMainDirectory = $RootURL . "resource/users/" . $userid . "/listings/";
                $pathtonew = "requestedlessons/";
                if (mkdir($PathToMainDirectory . $pathtonew)) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = FALSE;
            }
        } else {
            $result = false;
        }
        return $result;
    }
    
    public function CreateSEOFriendlyURL($parameterslist)
    {
        $result = '';
        for ($index = 0; $index < count($parameterslist); $index++) {
            $tempurl = strtolower($parameterslist[$index]);
            $tempurl = preg_replace('/[^(a-z|0-9)]+/simx', '-', $tempurl);
            $tempurl = trim($tempurl, "-");
            $result .= $tempurl . "/";
        }
        return $result;
    }

    public function GetSEOFriendlyURL($PageName, $Value, $ID = "", $Parameters = "")
    {
        $result = $this->_setting->GetRootURL();
        switch ($PageName) {
           case 'home':
                $result .= '';
                break;
            case 'register':
                $result .= 'register';
                break;
            case 'login':
                $result .= 'login';
                break;
            case 'profile':
                $result .= 'user/';
                break;
            case 'editprofile':
                $result .= 'profile/';
                break;
            case 'logout':
                $result .= 'logout';
                break;
            case 'resetpassword':
                $result .= 'resetpassword';
                break;
            case 'search':
                $result .="search";
                break;
            case 'postlesson':
                $result .="list";
                break;
            case 'requestalesson':
                $result .="request";
                break;
            case 'lesson_details':
                $result .="lesson/";
                break;
            case "editlesson":
                $result .="list/";
                break;
            case "forgot_password":
                $result .= "forgot";
                break;
            case "communication":
                $result .= "messages";
                break; 
            case "mylessons":
                $result .= "lessons/teach";
                break;
            case "teach":
                $result .= "lessons/teach";
                break;
            case "learn":
                $result .= "lessons/learn";
                break; 
            case 'makepayment':
                $result .= "makepayment/";
                break;
            case 'review':
                $result .= 'review/';
                break;
            case 'privacy_policy':
                $result .= 'privacy_policy';
                break;
            case 'term_of_use':
                $result .= 'terms_of_use';
                break;
            case 'faqs':
                $result .= 'faqs';
                break;
            case 'cashout':
                $result .= 'cashout';
                break;
            case 'transaction':
                $result .= 'transactions';
                break;
            case 'email_settings':
                $result .= 'email-settings/';
                break;
            default:
                $result = '';
        }
        
        $result .= $Value;

        if (isset($ID) && strlen($ID) > 0)
            $result .= $ID . "/";

        return $result;
    }
    
    public function GetWebConfigurations()
    {
        $result = '';
        $result = $this->_dbobj->GetSiteConfig();
        return $result;
    }
    
    public function GetCityAutoComplete($CityHunt,$StateHint='')
    {
       $result=''; 
       $City_Info = array();
       $City_Info = $this->_dbobj->GetCityData($CityHunt,$StateHint);  
       if(count($City_Info)>0)
       {
           for($i=0;$i<count($City_Info);$i++)
           {
            $json[]=array(
                    'id'=> $City_Info[$i]["locationid"],
                    'value'=> $City_Info[$i]["address"],
                    'label'=>$City_Info[$i]["address"]
                        );
           }
           $result = json_encode($json);
       }    
       return $result; 
    }
    
    public function GetUserProfileImage($UserId,$Type)
    {
        $result='';
        $UserImage = array();
        $UserImage = $this->_dbobj->GetUserProfilePhoto($UserId);
        if(count($UserImage)>0)
        {
            //$timthumb = $GLOBALS['RootURL'].'appcode/libraries/timthumb/timthumb.php';
            if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='check_p')
            {
                echo '<pre>';
                print_r($UserImage);
                echo '</pre>';
            }
            $ImageName = $UserImage[0]['image'];
            $result_i = $GLOBALS['DocumentRoot'] . "resource/users/{$UserId}/ProfileImages/{$ImageName}";
            if (!file_exists($result_i)) {
                $result = "{$GLOBALS['RootURL']}images/{$Type}_default-user.jpg";
            }
            else
            {
                $result = "{$GLOBALS['RootURL']}resource/users/{$UserId}/ProfileImages/{$ImageName}";
            }
        } 
        else
        {
           $result = "{$GLOBALS['RootURL']}images/{$Type}_default-user.jpg"; 
        }    
        return $result;
    }
    
    public function GetUserNewMessages($UserId)
    {
        $result='';
        $NewMessageCount = array();
        $NewMessageCount = $this->_dbobj->GetUserNewMessages($UserId);
        if(count($NewMessageCount)>0)
        {
            $result = $NewMessageCount[0]['Total'];
        }    
        
        return $result;
    }
    
    public function GetListingImages($ListingID, $ImageName,$ImageType)
    {
        $result='';
        if ($ImageName == '') {
            $ImageName = "{$GLOBALS['RootURL']}images/nophoto.png";
        }

        $TempImage = substr($ImageName, 0, 6);
        if ($TempImage != "https:") {
            $ImageName1 = $GLOBALS['DocumentRoot'] . "lessons/{$ListingID}/{$ImageName}";//{$ImageType}_
            if (!file_exists($ImageName1)) {

                $ImageName = "{$GLOBALS['RootURL']}images/nophoto.png";
            } else {

                $ImageName = $GLOBALS['RootURL'] . "lessons/{$ListingID}/{$ImageName}";//{$ImageType}_
            }
        }

        return $ImageName;
    }
    
    public function GetAllCategories()
    {
       $result = '';
        $Categories= array();
        $Categories = $this->_dbobj->GetAllCategories();
        if(count($Categories)>0)
        {
            $result = $Categories;
        }
        
        return $Categories; 
    }

    public function EncodeMessage($Message) {
        $Matches = $this->GetEmailInMessage($Message);
        if (count($Matches) > 0) {
            foreach ($Matches as $Match) {

                $StrCount = @strlen($Match);
                $replace = "";
                for ($i = 0; $i <= $StrCount; $i++) {
                    $replace .= "*";
                }
                $Message = str_replace($Match, $replace, $Message);
            }
        }
        return $Message;
    }

    public function GetEmailInMessage($Message) {

        preg_match_all("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i", $Message, $Matches);

        if(preg_match_all("/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/im", $Message, $WebMatches)){
//            print_r($WebMatches);
            $Matches[0][] = $WebMatches[0];
        }
        if(preg_match_all('/[+|-|\d]{7,}/', $Message, $PhoneMatch)){
//            print_r($PhoneMatch);
            $Matches[0][] = $PhoneMatch[0];
        }
//        print_r($PhoneMatch);
        return $Matches[0];
    }
    
    public function CalculatePrice($price)
    {
        $result = '';
        $web_config = $this->GetWebConfigurations();
        if($web_config!='')
        {
            for($i=0;$i<count($web_config);$i++)
            {
            if($web_config[$i]['name']=='servicefee')
            {
              $service_fee = $web_config[$i]['value'];   
              break;
            }  
             
            }
        }  
       $PAmount =  round(($service_fee * $price) / 100);
       $result = $PAmount;
       return $result;
    }
    
    public function GetCommunicationID($recieverid,$senderid)
    {
       $result = '';
        $Commuincation_ID = array();
        $Commuincation_ID = $this->_dbobj->CheckCommunication($recieverid,$senderid);
        if(count($Commuincation_ID)>0)
        {
            $result = $Commuincation_ID[0]['id'];
        }    
        
        return $result;  
    }
    
    public function StartCommunication($recieverid,$senderid)
    {
        $result = '';
        $LessonReviews = array();
        $LessonReviews = $this->_dbobj->StartNewCommunication($recieverid,$senderid);
        if(count($LessonReviews)>0)
        {
            $result = $LessonReviews;
        }    
        
        return $result;    
    }

    public function AddMessage($LessonID,$Requestid,$msg,$senderid,$reciverid,$CommuincationID,$Message_Type)
    {
        $result = '';
        $LessonReviews = array();
        $LessonReviews = $this->_dbobj->AddNewMessage($LessonID,$Requestid,$msg,$senderid,$reciverid,$CommuincationID,$Message_Type);
        if(count($LessonReviews)>0)
        {
            $result = $LessonReviews;
        }    
        
        return $result;  
    }
    
    public function GetPaypalSettings()
    {
      $RootURL = $this->_setting->GetRootURL();
      if(strpos($RootURL, 'localhost'))
      {
         $data['paypalurl'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
         $data['business']  = "bilal_1344330303_biz@dynamologic.com";
         $data['item_name'] = 'Learnyard';
      }   
      else
      {
         $data['paypalurl'] = 'https://www.paypal.com/cgi-bin/webscr';
         $data['business'] = "admin@learnyard.com";
         $data['item_name'] = 'Learnyard';
      }    
      return $data;
    }

    public function AddRequestPaymentDetails($Requestid,$txn_id,$payment_status,$amount,$data,$customer_id,$realprice,$service_fee,$payer_email,$PaymentType)
    {
        
        file_put_contents("{$_SERVER['DOCUMENT_ROOT']}learnyard/paypal/SubscriptionAdditionVariables.txt", 
                        "before adding to db".PHP_EOL, FILE_APPEND);
         $OldDetail = $this->_dbobj->GetBookingPaymentInfo($Requestid);
         $Cancelfound=false;
         
         if($OldDetail!='' && count($OldDetail)>0)
         {
             file_put_contents("{$_SERVER['DOCUMENT_ROOT']}learnyard/paypal/SubscriptionAdditionVariables.txt", 
                        "old details found".json_encode($OldDetail).PHP_EOL, FILE_APPEND);
             $PID = $OldDetail[0]['id'];
            return $this->_dbobj->UpdateBookingPaymentInfo($Requestid,$txn_id,$payment_status,$amount,$data,$customer_id,$realprice,$service_fee,$payer_email,$PaymentType,$PID);    
         }
         else
         {
            return $this->_dbobj->AddBookingPaymentInfo($Requestid,$txn_id,$payment_status,$amount,$data,$customer_id,$realprice,$service_fee,$payer_email,$PaymentType); 
         }         
    }
    
    public function GetStaticPageText($Type)
    {
       $result='';
       $LessonReviews = array();
       $LessonReviews = $this->_dbobj->GetStaticPageText($Type);
       if(count($LessonReviews)>0)
       {
            $result = $LessonReviews;
       }    
       return $result; 
       
    }

    public function GetAllFAQs()
    {
       $result='';
       $LessonReviews = array();
       $LessonReviews = $this->_dbobj->GetFAQs();
       if(count($LessonReviews)>0)
       {
            $result = $LessonReviews;
       }    
       return $result;  
    }
    
    public function GetCurrentSystemTime($DateTime)
    {
        $time_offset = @$_COOKIE['time_zone_offset'];
        $time_zone_name = @timezone_name_from_abbr("", -$_COOKIE['time_zone_offset']*60, $_COOKIE['time_zone_dst']);
      
        $old_timezone= date_default_timezone_get();
        
        $Date = date("m/d/Y  g:i a", strtotime($DateTime));
         
        
        if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='2')
         {
             echo '<pre>';
             echo "old time zone date:";
             echo '</br>';
             echo $Date;
             echo '</br>';
             echo '</pre>';
         } 
        
         if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='2')
         {
             echo '<pre>';
             echo "old time zone:";
             echo '</br>';
             echo $old_timezone;
             echo '</br>';
             echo $time_offset;
             echo '</br>';
             echo $time_zone_name;
             echo '</pre>';
         }    
//        $datetime = new DateTime($DateTime);
//        $la_time = new DateTimeZone($time_zone_name);
//        $datetime->setTimezone($la_time);
        
        
        $datetime = new DateTime($DateTime);
        $datetime->format('m/d/Y  g:i a');
        $la_time = new DateTimeZone($time_zone_name);
        $datetime->setTimezone($la_time);
        $DateTime = $datetime->format('m/d/Y  g:i a');
        
      
        date_default_timezone_set($time_zone_name);
        
        if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='2')
         {
             echo '<pre>';
             echo "new time zone:";
             echo '</br>';
             echo date_default_timezone_get();
             echo '</br>';
             echo '</pre>';
         }  
        
        $Date = date("m/d/Y  g:i a", strtotime($DateTime));
         
        
        if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='2')
         {
             echo '<pre>';
             echo "new time zone date:";
             echo '</br>';
             echo $Date;
             echo '</br>';
             echo '</pre>';
         } 
        date_default_timezone_set($old_timezone);   
        
        
        return $Date;
    }
    
    public function GetStatus($RequestID)
    {
        $result='';
        $Req_Details = array();
        $Req_Details = $this->_dbobj->GetStatus($RequestID);
        if(count($Req_Details)>0)
        {
            $result = $Req_Details[0]['Req_Status'];
        }    
        return $result; 
    }
    
    public function GetAdminEmailID()
    {
        $admin_id='';
        $web_config = $this->GetWebConfigurations();
        if($web_config!='')
        {
            for($i=0;$i<count($web_config);$i++)
            {
            if($web_config[$i]['name']=='cashout_admin')
            {
              $admin_id = $web_config[$i]['value']; 
              break;
            }  
             
            }
        } 
        
        return $admin_id;
    }
        
    public function GetMessageContent($MessageType,$UserType,$Content,$RequestID,$Page,
            $StudentName,$InstrucotrName,$Communication_ID,$LessonID,$LessonNAme,$Duration,$RequestDate,$Price,$RequestTime,$StudentID,$InstructorID, $Panel, $MessageID,$UserName, $SenderID, $RecieverID, $MessageTime='')
    {
        $Content = nl2br($Content);
        $result='';
        $Message='';
        $Style='';
        $current = date('Y-m-d H:i:s',  strtotime('now'));
        $offerdate = date('Y-m-d H:i:s', strtotime("$RequestDate $RequestTime"));
        
        
        if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='mm')
        {
            echo '<pre>';
            print_r(array($MessageType,$UserType,$Content,$RequestID,$Page,$StudentName,$InstrucotrName,$Communication_ID,$LessonID,$LessonNAme,$Duration,$RequestDate,$Price,$RequestTime,$StudentID,$InstructorID));
            echo '</pre>';
        }   
            switch($MessageType)
            {
                case 'request_msg':
                    $ReviewLink='';
                    if($UserType=='student')
                    {
                        $Message = $Content."<span class='date-time'>$MessageTime</span>";
                    }   
                    else
                    {
                        if($current>$offerdate)
                        {
                            $nolink = true;
                        }    
                        else
                        {
                          $review_link =  $this->GetSEOFriendlyURL('teach','');
                        $ReviewLink =<<<HTML
    <a href="$review_link" style="cursor:pointer;">Review request</a>
HTML;
                        } 
                        $Message = "$Content $ReviewLink. <span class='date-time'>$MessageTime</span>";
                    }
                break;
                case 'accept_msg':
                    $MakePaymentLink='';
                    if($UserType=='student')
                    {
                        $CheckPayment = $this->CheckBookingPayment($RequestID);
                        if($CheckPayment!='' && count($CheckPayment)>0)
                        {
                             for($pm=0;$pm<count($CheckPayment);$pm++)
                             {

                                 if($CheckPayment[$pm]['PStatus']=='cancel')
                                 {
                                    if($current>$offerdate)
                                      {
                                            $nolink = true;
                                      }    
                                      else
                                      {
                                        $n_link =  $this->GetSEOFriendlyURL('learn','');
                                        $invoice_link= $this->GetSEOFriendlyURL('makepayment', "?id=$RequestID");
                                        $MakePaymentLink =<<<HTML
                <a href="$n_link" style="cursor:pointer;">Make Payment</a>
HTML;
                                      }
                                 }
                             }
                        } 
                        else
                        {
                          if($current>$offerdate)
                          {
                                $nolink = true;
                          }    
                          else
                          {
                            $n_link =  $this->GetSEOFriendlyURL('learn','');
                            $invoice_link= $this->GetSEOFriendlyURL('makepayment', "?id=$RequestID");
                            $MakePaymentLink =<<<HTML
    <a href="$n_link" style="cursor:pointer;">Make Payment</a>
HTML;
                            }
                        }    
                        $Message = "&nbsp; has accepted your request. {$MakePaymentLink} <span class='date-time'>$MessageTime</span>";
                        $Style= 'style="width:auto !important;"';
                    }   
                    else
                    {
                        $Message = "You have accepted {$StudentName}'s request. <span class='date-time'>$MessageTime</span>";
                        $Style='style="display:none !important"';
                    }
                    
                break;
                
                case 'accept_offer':
                    $MakePaymentLink='';
                    if($UserType=='student')
                    {
                        $CheckPayment = $this->CheckBookingPayment($RequestID);
                        if($CheckPayment!='' && count($CheckPayment)>0)
                        {
                            for($pm=0;$pm<count($CheckPayment);$pm++)
                             {

                                 if($CheckPayment[$pm]['PStatus']=='cancel')
                                 {
                                    if($current>$offerdate)
                                      {
                                            $nolink = true;
                                      }    
                                      else
                                      {
                                        $n_link =  $this->GetSEOFriendlyURL('learn','');
                                        $invoice_link= $this->GetSEOFriendlyURL('makepayment', "?id=$RequestID");
                                        $MakePaymentLink =<<<HTML
                <a href="$n_link" style="cursor:pointer;">Make Payment</a>
HTML;
                                      }
                                 }
                             }
                        } 
                        else
                        {
                            
                           if($current>$offerdate)
                          {
                                $nolink = true;
                          }    
                          else
                          {
                            $n_link =  $this->GetSEOFriendlyURL('learn','');
                            $invoice_link= $this->GetSEOFriendlyURL('makepayment', "?id=$RequestID");
                            $MakePaymentLink =<<<HTML
    <a href="$n_link" style="cursor:pointer;">Make Payment Now</a>
HTML;
                            }
                        }    
                        $Message = "You have accepted the offer. {$MakePaymentLink}. <span class='date-time'>$MessageTime</span>";
                        $Style='style="display:none !important"';
                    }   
                    else
                    {
                        $Message = "&nbsp; has accepted the offer. <span class='date-time'>$MessageTime</span>";
                        $Style= 'style="width:auto !important;"';
                    } 
                break;    
                
                case 'offer_msg':
                    $ReviewLink='';
                    if($UserType=='student')
                    {
                        if($current>$offerdate)
                          {
                                $nolink = true;
                          }    
                          else
                          {
                            $Link = $this->GetSEOFriendlyURL('learn', '');
                        $ReviewLink =<<<HTML
    <a href="$Link" style="cursor:pointer;">Review request</a>
HTML;
                          }
                        $Message="&nbsp; has suggested a date time. {$ReviewLink}. <span class='date-time'>$MessageTime</span>";
                        $Style= 'style="width:auto !important;"';
                    }   
                    else
                    {
                       
                        $Message = "You have suggested a date and time. <span class='date-time'>$MessageTime</span>";
                        $Style='style="display:none !important"';
                    } 
                break; 
                case 'datetime_msg':
                    $ReviewLink='';
                    if($UserType=='student')
                    {
                        $Message = "You have suggested a date and time. <span class='date-time'>$MessageTime</span>";
                        $Style='style="display:none !important;"';
                    }   
                    else
                    {
                         if($current>$offerdate)
                          {
                                $nolink = true;
                          }    
                          else
                          {
                            $Link = $this->GetSEOFriendlyURL('teach', '');
                        $ReviewLink =<<<HTML
    <a href="$Link" style="cursor:pointer;">Review request</a>
HTML;
                          }
                        $Message = "&nbsp; has changed the date and time. {$ReviewLink}. <span class='date-time'>$MessageTime</span>";
                        $Style= 'style="width:auto !important;"';
                    } 
                break; 
                case 'decline_student':
                    $CModal='';
                    $ChangeDateTimeLink='';
                    $MModal ='';
                    $SugsestDateTimeLink='';
                    if($UserType=='student')
                    {
                        if($current>$offerdate){}   
                        else
                        {
                          $Link = $this->GetSEOFriendlyURL('learn', '');
                        $ChangeDateTimeLink =<<<HTML
    <a  href="$Link" style="cursor:pointer;">Change Date and Time</a>
HTML;
                        }    
                        $Message = "You have declined {$StudentName}'s offer. {$ChangeDateTimeLink}. <span class='date-time'>$MessageTime</span> {$CModal}";
                        $Style='style="display:none !important"';
                    }   
                    else
                    {
                        if($current>$offerdate){}   
                        else
                        {
                          $Link = $this->GetSEOFriendlyURL('teach', '');
                        $SugsestDateTimeLink =<<<HTML
    <a  href="$Link" style="cursor:pointer;">Suggest a date and time</a>
HTML;
                        }
                        
                        $Message = "&nbsp; has declined your offer. {$SugsestDateTimeLink} <span class='date-time'>$MessageTime</span> {$MModal}";
                        $Style= 'style="width:auto !important;"';
                    } 
                break; 
                case 'decline_instructor':
                    $CModal='';
                    $ChangeDateTimeLink='';
                    $MModal ='';
                    $SugsestDateTimeLink='';
                    if($UserType=='student')
                    {
                        if($current>$offerdate){}   
                        else
                        {
                         $Link = $this->GetSEOFriendlyURL('learn', '');
                       $ChangeDateTimeLink =<<<HTML
    <a href="$Link" style="cursor:pointer;">Change Date and Time</a>
HTML;
                        }
     
                        $Message = "&nbsp; has declined your request. {$ChangeDateTimeLink}. <span class='date-time'>$MessageTime</span> {$CModal}";
                        $Style= 'style="width:auto !important;"';
                    }   
                    else
                    {
                        if($current>$offerdate){}   
                        else
                        {
                        $Link = $this->GetSEOFriendlyURL('teach', '');
                        $SugsestDateTimeLink =<<<HTML
    <a href="$Link"  style="cursor:pointer;">Suggest a date and time</a>
HTML;
                        }
                        $Message = "You have declined {$StudentName}'s request. {$SugsestDateTimeLink}. <span class='date-time'>$MessageTime</span> {$MModal}";
                        $Style='style="display:none !important"';
                    } 
                break; 
                case 'receipr_msg':
                    
                    if($current>$offerdate){
                        $Transaction_link = $this->GetSEOFriendlyURL('transaction', '');
                        $MakePaymentLink =<<<HTML
    <a href="$Transaction_link" style="cursor:pointer;">Receipt</a>
HTML;
                    }else
                    {
                      $MakePaymentLink='';  
                    }
                    
                    if($UserType=='student')
                    {
                        $Message = "You have made payment.{$MakePaymentLink}. <span class='date-time'>$MessageTime</span>";
                        $Style='style="display:none !important"';
                    }   
                    else
                    {
                        $Message = "&nbsp; has made payment. {$MakePaymentLink}. <span class='date-time'>$MessageTime</span>";
                         $Style= 'style="width:auto !important;"';
                    } 
                break; 
                case 'cancel_student':
                    $CModal='';
                    $ChangeDateTimeLink='';
                    if($UserType=='student')
                    {
                        if($current>$offerdate){}   
                        else
                        {
                        $Link = $this->GetSEOFriendlyURL('learn', '');
                        $ChangeDateTimeLink =<<<HTML
    <a href="$Link" style="cursor:pointer;">Change Date and Time</a>
HTML;
                        }
                        $Message= "You have canceled the request. {$ChangeDateTimeLink}. <span class='date-time'>$MessageTime</span> {$CModal}";
                        $Style='style="display:none !important"';
                    }
                    else
                    {
                        $Message ="&nbsp; has canceled the request.<span class='date-time'>$MessageTime</span>";
                          $Style= 'style="width:auto !important;"';
                    }
                break;
                case 'cancel_instructor':
                    $CModal='';
                    $ChangeDateTimeLink='';
                    $MModal='';
                    $SugsestDateTimeLink='';
                    if($UserType=='student')
                    {
                        $Link = $this->GetSEOFriendlyURL('learn', '');
                        
                        $ChangeDateTimeLink =<<<HTML
    <a href="$Link" style="cursor:pointer;">Change Date and Time</a>
HTML;
                        $Message= "&nbsp; has canceled the offer. {$ChangeDateTimeLink}. <span class='date-time'>$MessageTime</span> {$CModal}";
                          $Style= 'style="width:auto !important;"';
                        
                    }
                    else
                    {
                        $Link = $this->GetSEOFriendlyURL('teach', '');
                        $SugsestDateTimeLink =<<<HTML
    <a href="$Link" style="cursor:pointer;">Suggest a date and time</a>
HTML;
                        $Message ="You have canceled the offer. {$SugsestDateTimeLink}. <span class='date-time'>$MessageTime</span> {$MModal}";
                        $Style='style="display:none !important"';
                    }
                break;
                case 'after_payment_student':
                    if($UserType=='student')
                    {
                        $Message="You have canceled the lesson. Refunds will be processed based on the lesson's cancellation policy. <span class='date-time'>$MessageTime</span> ";
                        $Style='style="display:none !important"';
                        
                    }   
                    else
                    {
                       $Message="&nbsp; has canceled the lesson. Refunds will be processed based on the lesson's cancellation policy. <span class='date-time'>$MessageTime</span>" ;
                       $Style= 'style="width:auto !important;"';
                       
                    }    
                break;
                
                case 'after_payment_instructor':
                    if($UserType=='student')
                    {
                        $Message= "&nbsp; has canceled the lesson. You will receive a full refund. <span class='date-time'>$MessageTime</span>";
                        $Style= 'style="width:auto !important;"';
                        
                    }
                    else
                    {
                        $Message ="You have canceled the lesson. A full refund will be given to the student. <span class='date-time'>$MessageTime</span>";
                        $Style='style="display:none !important"';
                        
                    }
                break;
                case 'simple':
                    
                    if($Panel=='left')
                    {
                      $Message=$Content; 
                    } 
                    else
                    {
                        $Message=$Content."<span class='date-time'>$MessageTime</span> ";
                    }    
                    
                    
                break;
                case 'image':
                    $Images = $this->GetAllMessageFiles('image',$MessageID);
                    if($Panel=='left')
                    {
                       if($_SESSION['UserId'] == $SenderID)
                       {
                          $name = 'You';
                       }    
                       else
                       {
                           $name = $UserName;
                       }    
                        
                        if(count($Images)==1)
                        {
                            $Message = "{$name} sent a photo";
                        }   
                        else
                        {
                           $count_img = count($Images);
                           $Message= "{$name} sent {$count_img} photos";
                        }    
                    }   
                    else
                    {
                        for($im=0;$im<count($Images);$im++)
                        {
                           $Filename = $Images[$im]['file'];
                           $FileURL = $GLOBALS['RootURL']."/message/{$MessageID}/{$Filename}"; 
                           if(count($Images)>1) 
                           {
                              $Message.= "<a href='$FileURL' target='_blank'><img src='{$GLOBALS['RootURL']}/app-code/libraries/timthumb/timthumb.php?src=$FileURL&h=200&w=200' height='200'width='200' height='200' width='200' alt='$Filename'></a> ";           
                           }   
                           else
                           {
                              $Message = "<a href='$FileURL' target='_blank'><img src='$FileURL' height='150' width='150' alt='$Filename'></a>";          
                           }   
                        }
                   }    
                  $Message = $Message."<span class='date-time'>$MessageTime</span>";  
                break;
                case 'file':
                    $Images = $this->GetAllMessageFiles('other',$MessageID);
                     $Message = $Content;
                    if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='mc')
                    {
                      echo '<pre>';
                      print_r($Images);
                      echo '<br>';
                      echo $result;
                      echo '</pre>';
                    }    
                   
                    if($Panel=='left')
                    {
                        if($result=='')
                        {   
                       if($_SESSION['UserId'] == $SenderID)
                       {
                          $name = 'You';
                       }    
                       else
                       {
                           $name = $UserName;
                       }    
                        
                        if(count($Images)==1)
                        {
                            $Message = "{$name} sent a file";
                        }   
                        else
                        {
                           $count_img = count($Images);
                           $Message= "{$name} sent {$count_img} files";
                        } 
                        }
                        else
                        {
                            $Message = $Content;
                        }    
                    }   
                    else
                    {
                       if($_SESSION['UserId'] == $SenderID)
                       {
                          $sclass = 'msg_file_span_right';
                       }    
                       else
                       {
                           $sclass = 'msg_file_span_left';
                       } 
                        $Message = $Content.'<br>';
                        for($im=0;$im<count($Images);$im++)
                        {
                           $Filename = $Images[$im]['file'];
                           $FileURL = $GLOBALS['RootURL']."/message/{$MessageID}/{$Filename}"; 
                           if(count($Images)>1) 
                           {
                              $Message.= "<span class='{$sclass}'>$Filename
                              <img src='{$GLOBALS['RootURL']}images/document-icon.png'>
                              <a href='$FileURL' target='_blank' style='float: right; text-decoration: none; cursor: pointer;'>Download</a>
                              </span>";           
                           }   
                           else
                           {
                              $Message .= "<span class='{$sclass}'>$Filename
                              <img src='{$GLOBALS['RootURL']}images/document-icon.png'>
                              <a href='$FileURL' target='_blank' style='float: right; text-decoration: none; cursor: pointer;'>Download</a>
                              </span>";          
                           }   
                        }
                   } 
                   $Message = $Message."<span class='date-time'>$MessageTime</span>";
                break;
                
                case 'cron_cancel':
                 $Message = "Your request is canceled because of no activity before offerdate<span class='date-time'>$MessageTime</span>";   
                break;
                default:
                     $Message=$Content."<span class='date-time'>$MessageTime</span>";
            }
         $result['content']=$Message; 
         $result['style']=$Style;
         if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='mm')
        {
            echo '<pre>';
            print_r($result);
            echo '</pre>';
        } 
        return $result;
    }
    
    public function CheckBookingPayment($RequestID)
    {
        $result = '';
        $RequestPayment = array();
        $RequestPayment = $this->_dbobj->GetBookingPaymentInfo($RequestID);
        if(count($RequestPayment)>0)
        {
            $result = $RequestPayment;
        }    
        
        return $result;    
    }

    public function GetRequestDetails($RequestID)
    {
        $result='';
        $Req_Details = array();
        $Req_Details = $this->_dbobj->GetRequestDetailInfo($RequestID);
        if(count($Req_Details)>0)
        {
            $result = $Req_Details;
        }    
        return $result; 
    }

    public function GetChangeDateTimeModalHTML($LessonID,$Req_id,$InstructorID,$Communication_ID,$lesson_name,$duration,$InstrucotrName,$RequestDate)
    {
        $lesson_link = $this->GetSEOFriendlyURL('lesson_details', "?id=$LessonID");
        $TimeDD=$this->GetTimeDropDown($duration,$Req_id,'learn');
        $Learnlink = $this->GetSEOFriendlyURL('communication', '');
        $Modal=<<<HTML
          <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="offer_dialog_$Req_id" >
          <div class="modal-dialog modal-lg" style="width:600px;">
          <div class="modal-content">
          <div class="message-container" style="margin:0px; border:0px;">
          <div class="msg-right-panel padding0" style="width:100%;" id="msg_main">
          <div class="box-footer">
          <input type="hidden" id="req_lessonid_$Req_id" name="req_lessonid_$Req_id" value="$LessonID"/>
          <input type="hidden" id="recieverid_$Req_id" name="recieverid_$Req_id" value="{$InstructorID}"/>
          <input type="hidden" id="instructorname_$Req_id" name="instructorname_$Req_id" value="{$InstrucotrName}"/>
          <input type="hidden" id="senderid_$Req_id" name="senderid_$Req_id" value="{$_SESSION['UserId']}"/>
          <input type="hidden" id="communication_id_$Req_id" name="communication_id_$Req_id" value="$Communication_ID"/>   
          <a href="$lesson_link" class="attach-link" style="text-transform:uppercase; border-right:0px !important; font-size:16px !important;"><strong>$lesson_name</strong></a>
          <div class="clear"></div>
          </div><!--box footer-->
          <div class="date_time_select"> 
          <div id="offer_err_$Req_id" class="all_errors"></div>
          <label class="attach-label">Select Date:</label>
          <a class="attach-link-small" onclick="javascript:GetDateTimePicker($Req_id,'learn','$RequestDate')">
          <strong>
          <span id="req_msg_date_$Req_id">Select a Date</span>
          <img src="{$GLOBALS['RootURL']}images/temp/down-arrow.png" alt="" style="margin:-3px 0 0 0px; vertical-align:middle;">
          </strong>
          </a>
          <input type="hidden" id="requestdate_new_$Req_id" name="requestdate_new_$Req_id" class="requestdate_2"/>
          <br>
          <label class="attach-label">Select Time:</label>
          {$TimeDD['white']}
<!--          <br>
          <label class="attach-label">Message:</label>
          <textarea name="req_message_$Req_id" id="req_message_$Req_id" class="textarea2 form-control"></textarea>-->
          </div>
          <a id="change_done_button_$Req_id" style="cursor:pointer !important" onclick="javascript:ChangeBookingRequestDateTime($Req_id,'$Learnlink')" 
          class="reply">Change Date & Time</a>
          <div id="loading-datetime_$Req_id" style="display: none; float:right; padding: 5px;">
          <img width="32px" height="32px" src="{$GLOBALS['RootURL']}images/ajax-loader.gif"></div>
          <div class="clear"></div>
          </div><!--message right panel-->
          <div class="clear"></div>
          </div><!--message container-->   
          </div><!--model content-->
          </div><!--modal dialog-->
          </div><!--modal main-->
HTML;
          $result = $Modal;
          
          return $result;
    }
        
    public function GetTimeDropDown($duration,$Req_id,$type)
    {
        $differenceinmins=0;
        if($duration!='')
        {
            if($duration=='0.5')
            {
                $differenceinmins = '30';
            }    
            else{
                for($Iterator=1;$Iterator<=12;$Iterator++)
                {
                    $current_value = "{$Iterator}";
                    $current_value_half = "{$Iterator}.5";
                    if($duration == $current_value)
                    {
                       $differenceinmins = $Iterator*60;
                    }
                    elseif($duration == $current_value_half)
                    {
                        $differenceinmins = $Iterator*60+30;
                    }
                }
            }
        } 
        
               $start = strtotime('12:00am');
               $end = strtotime('11:59pm');
               $by = $differenceinmins." mins";
              if($differenceinmins==0)
              {
                $Sec=60*60;  
              }   
              else
              {    
              $Sec = $differenceinmins*60;
              }
              
    $start = strtotime('12:00 AM');
    $end   = strtotime('11:59 PM');

    $White =<<<HTML
    <select name="req_time_new_$Req_id" id="req_time_new_$Req_id" class="form-control TimeDD_Select_White selectpicker" 
    onchange="javascript:SelectTimeWhite($Req_id,'$type');" data-style="btn-info" data-size="auto" title="Select Time">
HTML;
    
    $White.= "<option data-hidden='true' value=''>Select Time</option>"; 
    for($i = $start;$i<=$end;$i+=$Sec){ 
        $White.= "<option value='".date('g:i a', $i)."'>".date('g:i a', $i)."</option>";
     }
     $White.= "</select>";

    $TimeDD['white']=$White;
    return $TimeDD;
    }
  
    public function GetMakeOfferModalHTML($LessonID,$Req_id,$StudentID,$Communication_ID,$lesson_name,$Duration,$StudentName,$RequestDate)
    {
        $lesson_link = $this->GetSEOFriendlyURL('lesson_details', "?id=$LessonID");
        $TimeDD=$this->GetTimeDropDown($duration,$Req_id,'teach');
        $Teachlink = $this->GetSEOFriendlyURL('communication', '');
        $Modal=<<<HTML
          <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="offer_dialog_$Req_id" >
          <div class="modal-dialog modal-lg" style="width:600px;">
          <div class="modal-content">
          <div class="message-container" style="margin:0px; border:0px;">
          <div class="msg-right-panel padding0" style="width:100%;" id="msg_main">
          <div class="box-footer">
          <input type="hidden" id="req_lessonid_$Req_id" name="req_lessonid_$Req_id" value="$LessonID"/>
          <input type="hidden" id="recieverid_$Req_id" name="recieverid_$Req_id" value="{$StudentID}"/>
          <input type="hidden" id="customername_$Req_id" name="customername_$Req_id" value="{$StudentName}"/>
          <input type="hidden" id="senderid_$Req_id" name="senderid_$Req_id" value="{$_SESSION['UserId']}"/>
          <input type="hidden" id="communication_id_$Req_id" name="communication_id_$Req_id" value="$Communication_ID"/>    
          <a href="$lesson_link" class="attach-link" style="text-transform:uppercase; border-right:0px !important; font-size:16px !important;"><strong>$lesson_name</strong></a>
          <div class="clear"></div>
          </div><!--box footer-->
          <div class="date_time_select"> 
          <div id="offer_err_$Req_id" class="all_errors"></div>
          <label class="attach-label">Select Date:</label>
          <a class="attach-link-small" onclick="javascript:GetDateTimePicker($Req_id,'teach','$RequestDate')">
          <strong>
          <span id="req_msg_date_$Req_id">Select a Date</span>
          <img src="{$GLOBALS['RootURL']}images/temp/down-arrow.png" alt="" style="margin:-3px 0 0 0px; vertical-align:middle;">
          </strong>
          </a>
          <input type="hidden" id="requestdate_new_$Req_id" name="requestdate_new_$Req_id" class="requestdate_2"/>
          <br>
          <label class="attach-label">Select Time:</label>
          {$TimeDD['white']}
          <input type="hidden" id="requesttime_new_$Req_id" name="requesttime_new_$Req_id" class="requesttime_2"/>
          <br>
          <label class="attach-label">Price:</label>
          <input type="text" id="price_new_$Req_id" name="price_new_$Req_id" class="form-control reg-field dollor-sign" style="padding-left: 20px ! important;" value="" onblur="javascript:AddPricetoMessage($Req_id);"/>
          <br>
          <label class="attach-label">Message:</label>
          <textarea name="req_message_$Req_id" id="req_message_$Req_id" class="textarea2 form-control"></textarea>
          </div>
          <a id="makeoffer_done_button_$Req_id" style="cursor:pointer !important" onclick="javascript:MakeanOffer($Req_id,'$Teachlink')" 
          class="reply">Make an Offer</a>
          <div id="loading-offer_$Req_id" style="display: none; float:right; padding: 5px;">
          <img width="32px" height="32px" src="{$GLOBALS['RootURL']}images/ajax-loader.gif"></div>
          <div class="clear"></div>
          </div><!--message right panel-->
          <div class="clear"></div>
          </div><!--message container-->   
          </div><!--model content-->
          </div><!--modal dialog-->
          </div><!--modal main-->
HTML;
          $result = $Modal;
          
          return $result; 
    }

    public function GetAllMessageFiles($Type,$MessageID)
    {
        $result='';
        $Files = array();
        $Files = $this->_dbobj->GetAllMessageFiles($MessageID,$Type);
        if(count($Files)>0)
        {
            $result = $Files;
            
        }    
        return $result;
    }
    
    public function GetCategoryDropDown($CategoryName='')
    {
        $result='';
        $selected='';
        $category='';
        $realname='';
     $RRcategoryDD='';
        
        if($CategoryName!='')
        {
            $CategoryName=$CategoryName.'/';
        }    
        
        $Categories = $this->GetAllCategories();
        if(count($Categories)>0)
        {    
          $categoryDD=<<<HTML
   <select id="category" name="category" class="form-control fl option-field selectpicker" data-style="btn-inverse"  
       title="CATEGORIES"
       onchange="javascript:SearchFormSubmit();">
HTML;
           if($CategoryName!=''&&$CategoryName=='all')
           {
             $selected = "selected='selected'" ;  
             $category ='all';
             $realname = $parameterslist[]=$Categories[$i]['name'];
           }
              $parameterslist='';
              $parameterslist[]='all';
              $Category_URL = $this->CreateSEOFriendlyURL($parameterslist);
           $categoryDD.=<<<HTML
   <option data-hidden='true' value="">CATEGORIES</option>
       <option value="{$Category_URL}" $selected>All</option>  
HTML;
          for($cat=0;$cat<count($Categories);$cat++)
          {
              $selected='';
              $parameterslist='';
              $parameterslist[]=$Categories[$cat]['name'];
              $Category_URL = $this->CreateSEOFriendlyURL($parameterslist);
              if($CategoryName!=''&&$Category_URL==$CategoryName)
              {
                $selected = "selected='selected'" ; 
                $category =$Categories[$cat]['id'];
                $realname = $parameterslist[]=$Categories[$cat]['name'];
              } 
              
              if($Categories[$cat]['id']==9)
              {    
              $RRcategoryDD=<<<HTML
   <option value="{$Category_URL}" $selected>{$Categories[$cat]['name']}</option>
HTML;
              }
              else
              {
                 $categoryDD.=<<<HTML
   <option value="{$Category_URL}" $selected>{$Categories[$cat]['name']}</option>
HTML;
            }    
          }
          $categoryDD.=$RRcategoryDD;
          $categoryDD.=<<<HTML
   </select>
HTML;
        }
        $result['html']=$categoryDD;
        $result['selected_category']=$category;
        $result['real_name']=$realname;
//        print_r($result);
//        exit;
        return $result; 
    }
  /*
    public function CreateLessonURL($LessonName)
    {
       
       $GetOldSameLessons = $this->_dbobj->GetOldSameLessons($LessonName);
       if($GetOldSameLessons!='' && count($GetOldSameLessons)>0)        
       {
           if($GetOldSameLessons[0]['Total']>0)
           {
              $LC = $GetOldSameLessons[0]['Total']+1;
              $LessonName .= "{$LC}" ; 
           }     
       }    
       $LessonURLP[] = $LessonName;
       $LURL = $this->CreateSEOFriendlyURL($LessonURLP);
       return $LURL;
    }
    
    public function CreateUserURL($FirstName,$LastName)
    {
       $Name = "$FirstName $LastName";
       $GetOldSameUsers = $this->_dbobj->GetOldSameUsers($FirstName,$LastName);
       if($GetOldSameUsers!='' && count($GetOldSameUsers)>0)        
       {
           if($GetOldSameUsers[0]['Total']>0)
           {
              $LC = $GetOldSameUsers[0]['Total']+1;
              $Name .= " {$LC}" ; 
           } 
       }    
       $UserURLP[] = "$Name";
       $UURL = $this->CreateSEOFriendlyURL($UserURLP);
       return $UURL; 
    }
    */
  
    public function CreateLessonURL($LessonName)
    {
        $LC=1;
       $GetOldSameLessons = $this->_dbobj->GetOldSameLessons($LessonName);
       if($GetOldSameLessons!='' && count($GetOldSameLessons)>0)        
       {
              $LC = $GetOldSameLessons[0]['url_max_val']+1;
              $LessonName .= "'{$LC}" ;    
       }    
       $LessonURLP[] = $LessonName;
       $LURL['url'] = $this->CreateSEOFriendlyURL($LessonURLP);
       $LURL['max_val']=$LC;
       return $LURL;
    }
    
    public function CreateUserURL($FirstName,$LastName)
    {
        $LC=1;
       $Name = "$FirstName $LastName";
       $GetOldSameUsers = $this->_dbobj->GetOldSameUsers($FirstName,$LastName);
       if($GetOldSameUsers!='' && count($GetOldSameUsers)>0)        
       {
              $LC = $GetOldSameUsers[0]['url_max_val']+1;
              $Name .= "'{$LC}" ; 
       }    
       $UserURLP[] = "$Name";
       $UURL['url'] = $this->CreateSEOFriendlyURL($UserURLP);
       $UURL['max_val']=$LC;
       return $UURL; 
    }
    
    public function GetUserIDByURL($UURL)
    {
       $UID = '';
       $GetUser = $this->_dbobj->GetUserByURL($UURL);
       if($GetUser!='' && count($GetUser)>0)        
       {
         if(isset($_REQUEST['debug'])&&($_REQUEST['debug']=='sp'||$_REQUEST['debug']=='check_p'))
         {
             echo '<pre>';
             print_r($GetUser);
            echo '</pre>';
         }
           $UID= $GetUser[0]['id'];
       }
       return $UID; 
    }
    
    public function GetLessonByURL($LURL)
    {
       $UID = '';
       $GetUser = $this->_dbobj->GetLessonUsingURL($LURL);
       if($GetUser!='' && count($GetUser)>0)        
       {
           $UID= $GetUser[0]['id'];
       }
       return $UID; 
    }
    
    public static function CheckDevice()
    {
        $tablet = 'no';
        $mobile = 'no';
        $tablet_browser = 0;
        $mobile_browser = 0;
 
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $tablet_browser++;
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-');

        if (in_array($mobile_ua,$mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
              $tablet_browser++;
                    }
                }

        
        if ($tablet_browser > 0) {
            $tablet = 'yes';
        }
        else if ($mobile_browser > 0) {
            $mobile = 'yes';
        }
        
        $result['mobile']=$mobile;
        $result['tablet']= $tablet;
        return $result;
    }
    
    public function TimeDropDown($ValueSelected='')
    {
        $one = '';
        $one_half = '';
        $two = '';
        $two_half = '';
        $three = '';
        $three_half = '';
        $four = '';
        $four_half = '';
        $five = '';
        $five_half = '';
        $six = '';
        $six_half = '';
        $seven = '';
        $seven_half = '';
        $eight = '';
        $eight_half = '';
        $nine = '';
        $nine_half = '';
        $ten = '';
        $ten_half = '';
        $eleven = '';
        $eleven_half = '';
        $tweleve = '';
        $tweleve_half = '';
        $thirteen= '';
        $thirteen_half = '';
        $fourteen = '';
        $fourteen_half = '';
        $fiften = '';
        $fiften_half = '';
        $sixteen = '';
        $sixteen_half = '';
        $seventeen = '';
        $seventeen_half = '';
        $eighteen = '';
        $eighteen_half = '';
        $nineteen = '';
        $nineteen_half = '';
        $twenty = '';
        $twenty_half = '';
        $twenty1 = '';
        $twenty1_half = '';
        $twenty2 = '';
        $twenty2_half = '';
        $twenty3 = '';
        $twenty3_half = '';
        $twenty4 = '';
        $twenty4_half = '';
        $ValueSelected = strtolower($ValueSelected);
         switch($ValueSelected)
         {
           case '12:00 am':
               $one = "selected='selected'";
           break;
           case '12:30 am':
               $one_half = "selected='selected'";
           break;
       case '1:00 am':
           $two =  "selected='selected'";
           break;
       case '1:30 am':
           $two_half =  "selected='selected'";
           break;
       case '2:00 am':
           $three =  "selected='selected'";
           break;
       case '2:30 am':
            $three_half =  "selected='selected'";
           break;
       case '3:00 am':
            $four =  "selected='selected'";
           break;
       case '3:30 am':
            $four_half =  "selected='selected'";
           break;
       case '4:00 am':
            $five =  "selected='selected'";
           break;
       case '4:30 am':
           $five_half =  "selected='selected'";
           break;
       case '5:00 am':
           $six =  "selected='selected'";
           break;
       case '5:30 am':
           $six_half =  "selected='selected'";
           break;
       case '6:00 am':
           $seven  =  "selected='selected'";
           break;
       case '6:30 am':
            $seven_half  =  "selected='selected'";
           break;
       case '7:00 am':
           $eight = "selected='selected'";
           break;
       case '7:30 am':
            $eight_half = "selected='selected'";
           break;
       case '8:00 am':
           $nine = "selected='selected'";
           break;
       case '8:30 am':
           $nine_half = "selected='selected'";
           break;
       case '9:00 am':
           $ten = "selected='selected'";
           break;
       case '9:30 am':
           $ten_half = "selected='selected'";
           break;
       case '10:00 am':
           $eleven = "selected='selected'";
           break;
       case '10:30 am':
           $eleven_half  = "selected='selected'";
           break;
       case '11:00 am':
           $tweleve  = "selected='selected'";
           break;
       case '11:30 am':
           $tweleve_half  = "selected='selected'";
           break;
       case '12:00 pm':
           $thirteen  = "selected='selected'";
           break;
       case '12:30 pm':
           $thirteen_half  = "selected='selected'";
           break;
       case '1:00 pm':
           $fourteen  = "selected='selected'";
           break;
       case '1:30 pm':
           $fourteen_half =  "selected='selected'";
           break;
       case '2:00 pm':
           $fiften  = "selected='selected'";
           break;
       case '2:30 pm':
           $fiften_half  = "selected='selected'";
           break;
       case '3:00 pm':
           $sixteen  = "selected='selected'";
           break;
       case '3:30 pm':
           $sixteen_half  = "selected='selected'";
           break;
       case '4:00 pm':
           $seventeen  = "selected='selected'";
           break;
       case '4:30 pm':
           $seventeen_half  = "selected='selected'";
           break;
       case '5:00 pm':
           $eighteen  = "selected='selected'";
           break;
       case '5:30 pm':
           $eighteen_half = "selected='selected'";
           break;
       case '6:00 pm':
           $nineteen  = "selected='selected'";
           break;
       case '6:30 pm':
           $nineteen_half  = "selected='selected'";
           break;
       case '7:00 pm':
           $twenty  = "selected='selected'";
           break;
       case '7:30 pm':
           $twenty_half  = "selected='selected'";
           break;
       case '8:00 pm':
            $twenty1  = "selected='selected'";
           break;
       case '8:30 pm':
           $twenty1_half  = "selected='selected'";
           break;
       case '9:00 pm':
           $twenty2  = "selected='selected'";
           break;
       case '9:30 pm':
           $twenty2_half  = "selected='selected'";
           break;
       case '10:00 pm':
           $twenty3  = "selected='selected'"; 
           break;
       case '10:30 pm':
           $twenty3_half = "selected='selected'";
           break;
       case '11:00 pm':
           $twenty4 = "selected='selected'";
           break;
       case '11:30 pm':
           $twenty4_half = "selected='selected'";
           break;  
       default:
           break;
       
         }
        $TimeDropDown=<<<HTML
        <option value="8:00 am" $nine>08:00 am</option>      
        <option value="8:30 am" $nine_half>08:30 am</option>      
        <option value="9:00 am" $ten>09:00 am</option>      
        <option value="9:30 am" $ten_half>09:30 am</option>      
        <option value="10:00 am" $eleven>10:00 am</option>      
        <option value="10:30 am" $eleven_half>10:30 am</option>      
        <option value="11:00 am" $tweleve>11:00 am</option>      
        <option value="11:30 am" $tweleve_half>11:30 am</option>      
        <option value="12:00 pm" $thirteen>12:00 pm</option>      
        <option value="12:30 pm" $thirteen_half>12:30 pm</option>      
        <option value="1:00 pm" $fourteen>1:00 pm</option>      
        <option value="1:30 pm" $fourteen_half>1:30 pm</option>      
        <option value="2:00 pm" $fiften>2:00 pm</option>      
        <option value="2:30 pm" $fiften_half>2:30 pm</option>      
        <option value="3:00 pm" $sixteen>3:00 pm</option>      
        <option value="3:30 pm" $sixteen_half>3:30 pm</option>      
        <option value="4:00 pm" $seventeen>4:00 pm</option>      
        <option value="4:30 pm" $seventeen_half>4:30 pm</option>      
        <option value="5:00 pm" $eighteen>5:00 pm</option>      
        <option value="5:30 pm" $eighteen_half>5:30 pm</option>      
        <option value="6:00 pm" $nineteen>6:00 pm</option>      
        <option value="6:30 pm" $nineteen_half>6:30 pm</option>      
        <option value="7:00 pm" $twenty>7:00 pm</option>      
        <option value="7:30 pm" $twenty_half>7:30 pm</option>      
        <option value="8:00 pm" $twenty1>8:00 pm</option>      
        <option value="8:30 pm" {$twenty1_half}>8:30 pm</option>      
        <option value="9:00 pm" {$twenty2}>9:00 pm</option>      
        <option value="9:30 pm" {$twenty2_half}>9:30 pm</option>      
        <option value="10:00 pm" {$twenty3}>10:00 pm</option>      
        <option value="10:30 pm" {$twenty3_half}>10:30 pm</option>      
        <option value="11:00 pm"  $twenty4>11:00 pm</option>      
        <option value="11:30 pm" $twenty4_half>11:30 pm</option>
        <option value="12:00 am" $one>12:00 am</option>      
        <option value="12:30 am" $one_half>12:30 am</option>      
        <option value="1:00 am" $two>01:00 am</option>     
        <option value="1:30 am" $two_half>01:30 am</option>      
        <option value="2:00 am" $three>02:00 am</option>      
        <option value="2:30 am" $three_half>02:30 am</option>      
        <option value="3:00 am" $four>03:00 am</option>      
        <option value="3:30 am" $four_half>03:30 am</option>      
        <option value="4:00 am" $five>04:00 am</option>      
        <option value="4:30 am" $five_half>04:30 am</option>      
        <option value="5:00 am" $six>05:00 am</option>      
        <option value="5:30 am" $six_half>05:30 am</option>      
        <option value="6:00 am" $seven>06:00 am</option>      
        <option value="6:30 am" $seven_half>06:30 am</option>     
        <option value="7:00 am" $eight>07:00 am</option>      
        <option value="7:30 am" $eight_half>07:30 am</option>    
HTML;
      return $TimeDropDown;  
    }
    
    public function CheckOldResponseExists($UserID){
        return $this->_dbobj->GetOldResponseExists($UserID);
    }
    
    public function AddToRequestCount($UserID){
        return $this->_dbobj->AddUserRequestCount($UserID);
    }
    
    public function InsertRequestCount($UserID)
    {
       return $this->_dbobj->InsertUserRequestCount($UserID); 
    } 
    
    public function AddResponseCountandTimeDifference($UserID,$TimeDifference)
    {
       return $this->_dbobj->AddResponseCountandTimeDifference($UserID,$TimeDifference); 
    }
    
    public function GetRequestDateTimeInfo($RequestID)
    {
       return $this->_dbobj->GetRequestDateTime($RequestID);     
    }
    
    public function GetMessagebyInstructorCount($RequestID,$Instrtuctor)
    {
       $Result=0;
       $MCount = $this->_dbobj->GetMessagebyInstructorCount($RequestID,$Instrtuctor);
       if($MCount!=''&&count($MCount)>0)
       {
           $Result = $MCount[0]['total'];
       } 
       return $Result;
    }        

    public function GetUserResponseRate($UserID)
    {
        $Result = '';
        $Request_R_info = $this->CheckOldResponseExists($UserID);
        if($Request_R_info!=''&&count($Request_R_info)>0)
        {
          $Total_Requests = $Request_R_info[0]['total_request'];
          $Total_Responses = $Request_R_info[0]['total_response']; 
          
           if($Total_Responses<1)
           {
               $Result = "100%";
           }   
           else
           {
                $UserLessonRequests = $this->_dbobj->GetUserLessonRequests($UserID);
                if($UserLessonRequests!='' && count($UserLessonRequests)>0)
                {
                    $diference=0;
                    for($i=0;$i<count($UserLessonRequests);$i++)
                    {
                        $diff = $this->TimeDifferenceinHours($UserLessonRequests[$i]['datetime']);
                        
                         if($diff<24)
                         {
                           $MessageTime =  $this->GetMessagebyInstructorCount($UserLessonRequests[$i]['rid'],$UserID);
                           if($MessageTime==0)
                             {
                                 $diference++;
                             }
                         }    
                    }
                    
                    if($diference>0)
                    {
                        
                        $Total_Requests = $Total_Requests-$diference;
                    } 

                    if($Total_Requests>0)
                    {
                        $PercentageResult = ($Total_Responses/$Total_Requests)*100;
                        $Result = "$PercentageResult%";
                    } 
                    else
                    {
                        $Result = "100%";
                    }
                }   
                else
                {
                    if($Total_Responses>$Total_Requests)
                    {
                        $Result = "100%";
                    } 
                    else
                    {
                        $PercentageResult = ($Total_Responses/$Total_Requests)*100;
                        $Result = "$PercentageResult%";
                    }    
                }  
           }    
           
        }
        else
        {
           $Result = "100%";  
        }    
        return $Result;
    } 
    
    public function GetUserResponseTime($UserID)
    {
        $Result='';
        $Request_R_info = $this->CheckOldResponseExists($UserID);
        if($Request_R_info!=''&&count($Request_R_info)>0)
        {
            $Total_Requests = $Request_R_info[0]['total_request'];
            $TimeDifferenceTotal = $Request_R_info[0]['time_for_first_response'];
            
            $Average = round($TimeDifferenceTotal/$Total_Requests);
            if($Average==0)
            {
                $Result = 'within a day';
            }
            elseif($Average<6)
            {
                $Result = 'within a few hours'; 
            } 
            elseif($Average>=6 && $Average<24)
            {
                $Result = 'within a day'; 
            } 
            elseif($Average>=24 && $Average<96)
            {
                $Result = 'within a few days'; 
            } 
            elseif($Average>=96 && $Average<168)
            {
                $Result = 'within a week'; 
            } 
            else
            {
               $Result = 'week or longer';  
            }    
        }
        else
        {
           $Result = 'within a day';
        }  
        
        return $Result;
    }        

    public function TimeDifferenceinHours($Time)
    {
        $hours_difference = 0;
        $h_diff= 0;
        $d_diff= 0;
        $m_diff= 0;
        $limit_date = date_create(date("Y-m-d H:i:s", (strtotime($Time))));
     //   echo date("Y-m-d H:i:s", time());
        $time1 = date_create(date("Y-m-d H:i:s", time()));
        $interval = date_diff($time1, $limit_date);
        $day_diff = $interval->days;
        $hours_left = $interval->h;
        $month_left = $interval->m;
        $mins_left = $interval->i;
      //  echo "<pre>";print_r($interval);echo "</pre>";
        if($month_left>0)
        {
            $m_diff = $month_left*30*24;
        }    
        if($day_diff>0)
        {
            $d_diff = $day_diff*24;
        }    
        if($hours_left>0)
        {
            $h_diff = $hours_left;
        }    
        
        $hours_difference = $h_diff+$d_diff+$m_diff;
        return $hours_difference;
    }
    
}
?>
