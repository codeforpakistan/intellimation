<?php
include_once 'settings.php';

class Common_db
{
   public function GetUSerBaicInformation($UserID)
   {
       $Result= '';
       $Query = "SELECT u.*,city.name as city, state.abbreviation as state,
        DATE_FORMAT(`last_login`, '%Y-%m-%d') as lastlogin,
        DATE_FORMAT(`datetime`, '%Y-%m-%d') as joindate   
        FROM
        user as u INNER JOIN city ON city.id = u.locationid INNER JOIN state ON state.id = city.stateid 
        INNER JOIN country ON country.id = state.countryid WHERE u.id = $UserID";
        if(isset($_REQUEST['debug'])&&$_REQUEST['debug']=='check_p')
         {
            echo '<pre>';
            echo $Query;
            echo '</pre>';
         } 
       $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
       $MyPrepare->execute();
       $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
       return $Result;    
   }
     
   public function GetSiteConfig()
   {
       $result = '';
       $Query = "SELECT * FROM webconfig";
       $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
       $MyPrepare->execute();
       $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
       return $Result;
        
   }
   
   public function GetCityData($CityHint,$StateHint='')
   {
       $Result = '';
       $StateHint = '';
       if($StateHint!='')
       {
           $StateHint ="AND state.abbreviation like '%$CityHint%' AND state.status =1";
       }    
       $Query = "SELECT city.id as locationid, city.name as city, state.abbreviation as state,
       CONCAT(city.name,', ',state.abbreviation) as address FROM  
       city INNER JOIN state ON state.id = city.stateid
       INNER JOIN country ON  country.id = state.countryid
       WHERE city.name LIKE '%$CityHint%' AND city.status =1
       $StateHint
       ";
//       echo $Query;
       $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
       $MyPrepare->execute();
       $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
       return $Result;
   }
  
   public function GetUserProfilePhoto($UserId)
   {
       $Result= '';
       $Query = "SELECT * FROM user_image WHERE userid = $UserId";
       $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
       $MyPrepare->execute();
       $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
       return $Result;  
       
   }

   public function GetUserNewMessages($UserId)
   {
       $Result = '';
       $Query = "SELECT count(*) as Total FROM messages WHERE reciever = $UserId and is_read=0";
       $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
       $MyPrepare->execute();
       $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
       return $Result;
   }
   
   public function GetAllCategories()
   {
        $Result='';
        $Query = "SELECT * FROM categories ORDER BY name ASC";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;
   }
   
   public function CheckCommunication($recieverid,$senderid)
   {
        $result='';
        $Query = "SELECT * FROM communication WHERE 
        (user_one = $recieverid AND user_two = $senderid) OR (user_one = $senderid AND user_two = $recieverid)";
        //echo $Query;
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;  
    }
    
   public function StartNewCommunication($recieverid,$senderid)
   {
        $Result='';
        $currentdate = date('Y-m-d H:i:s' , strtotime('now'));
        $Query = "INSERT INTO communication SET
            user_one=?,
            user_two=?";
            $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
            $MyArray = array($recieverid,$senderid);
            $Response = $MyPrepare->execute($MyArray);
            if($Response)
            {
               $Result = $GLOBALS['LYDBConnect']->lastInsertId();
            }
            return $Result; 
    }
    
   public function AddNewMessage($lessonid,$Requestid,$msg,$senderid,$reciverid,$CommuincationID,$Message_Type)
   {
        $Result='';
        $currentdate = date('Y-m-d H:i:s' , strtotime('now'));
        $Query = "INSERT INTO messages SET
            lessonid=?,
            sender=?,
            reciever=?,
            datetime=?,
            req_id=?,
            content=?,
            communication_id=?,
            message_type = (SELECT id FROM message_type WHERE name=?)";
            $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
            $MyArray = array($lessonid,$senderid,$reciverid,$currentdate,$Requestid,$msg,$CommuincationID,$Message_Type);
            $Response = $MyPrepare->execute($MyArray);
            if($Response)
            {
               $Result = $GLOBALS['LYDBConnect']->lastInsertId();
               $Query2 = "UPDATE communication SET datetime= now() WHERE id= $CommuincationID";
               $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query2);
               $Response = $MyPrepare->execute();
            }
            return $Result; 
    }

   public function AddBookingPaymentInfo($Requestid,$txn_id,$payment_status,$amount,$data,$customer_id, $realprice,$service_fee,$payer_email,$PaymentType)
   {
        $Result='';
        $currentdatetime = date('Y-m-d H:i:s', strtotime('now'));
        $data = mysql_escape_string($data);
        $Query="INSERT INTO payment SET
          request_id=$Requestid,
          amount=$amount,
          transaction_id= '$txn_id',
          payment_method=(SELECT id FROM payment_method WHERE name='$PaymentType'),
          payment_status=(SELECT id FROM payment_status WHERE name='paid'),
          payment_data='$data',
          datetime='$currentdatetime',
          payer_email='$payer_email',
          customer_id= $customer_id,
          realprice= $realprice,
          servicefee=$service_fee";
                @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/paypal/SubscriptionVariables.txt", 
                        $Query.PHP_EOL, FILE_APPEND);
        
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $Response = $MyPrepare->execute();
        @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/paypal/SubscriptionVariables.txt", 
                        $Response.PHP_EOL, FILE_APPEND);
        if($Response)
        {
          $ID_New = $GLOBALS['LYDBConnect']->lastInsertId();
          $Query_2 = "UPDATE request SET status = (SELECT id FROM request_status WHERE name='Paid'),
          datetime ='$currentdatetime'
          WHERE id=$Requestid";
          @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/paypal/SubscriptionVariables.txt", 
                        $Query_2.PHP_EOL, FILE_APPEND);
          $MyPrepare_2 = $GLOBALS['LYDBConnect']->prepare($Query_2);
          $Response_2 = $MyPrepare_2->execute();
          $Result = $ID_New;
        }
        return $Result; 
    }

    public function UpdateBookingPaymentInfo($Requestid,$txn_id,$payment_status,$amount,$data,$customer_id,$realprice,$service_fee,$payer_email,$PaymentType,$PID)
    {
          $Result='';
          $Query="UPDATE payment SET
          request_id=$Requestid,
          amount=$amount,
          transaction_id= '$txn_id',
          payment_method=(SELECT id FROM payment_method WHERE name='$PaymentType'),
          payment_status=(SELECT id FROM payment_status WHERE name='paid'),
          payment_data='$data',
          datetime='$currentdatetime',
          payer_email='$payer_email',
          customer_id= $customer_id,
          realprice= $realprice,
          servicefee=$service_fee 
            WHERE id = $PID";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
            
        @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/paypal/SubscriptionVariables.txt", 
        $Query.PHP_EOL, FILE_APPEND);
        
        $Response = $MyPrepare->execute();
        @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/paypal/SubscriptionVariables.txt", 
                        $Response.PHP_EOL, FILE_APPEND);
        
        if($Response)
        {
          $ID_New = true;
          $Query_2 = "UPDATE request SET status = (SELECT id FROM request_status WHERE name='Paid')
          WHERE id=$Requestid";
          @file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/paypal/SubscriptionVariables.txt", 
                        $Query_2.PHP_EOL, FILE_APPEND);
          $MyPrepare_2 = $GLOBALS['LYDBConnect']->prepare($Query_2);
          $Response_2 = $MyPrepare_2->execute();
          $Result = true;
        }
    }


    public function GetStaticPageText($Type)
   {
        $result='';
        $Query="SELECT static_pages.* FROM pages INNER JOIN static_pages ON static_pages.page_id = pages.id
            WHERE pages.name =?";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyArray = array($Type);
        $Response = $MyPrepare->execute($MyArray);
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;  
    }
    
   public function GetFAQs()
   {
       $result='';
        $Query="SELECT * FROM faqs INNER JOIN faq_sort
        ON faqs.id = faq_sort.faq_id   
        ORDER BY faq_sort.sort_order ASC";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $Response = $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;   
    }
    
   public function GetStatus($RequestID)
   {
        $Result='';
        $Query="SELECT r.*,rs.name as Req_Status
        FROM request_status as rs INNER JOIN request as r 
        ON rs.id = r.status 
        WHERE r.id = $RequestID";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;
    }
    
   public function GetBookingPaymentInfo($RequestID)
   {
        $result='';
        $Query = "SELECT *,ps.name as PStatus,p.datetime as paymentdate
        FROM payment_status as ps INNER JOIN payment as p ON p.payment_status = ps.id
        INNER JOIN request as r on r.id = p.request_id
        WHERE r.id = $RequestID 
        AND is_close !='yes' ORDER BY p.id ASC";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result; 
    }
    
    
   public function GetRequestDetailInfo($RequestID)
   {
       $Result='';
        $Query = "SELECT *, L.name as LessonName, L.id  as LessonID,
        L.userid as InstructorID, R.customer as StudentID, L.duration AS L_Duration
        FROM request as R INNER JOIN lesson as L ON L.id = R.lessonid
        WHERE R.id = $RequestID";
//        if(isset($_REQUEST['debug']))
//        {    
//          echo $Query;
//        }
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;   
   }
   
   public function GetAllMessageFiles($MessageID,$Type)
   {
       $Result='';
        $Query = "SELECT * FROM message_files
        WHERE messageid  = $MessageID AND file_type='$Type'";
//        echo $Query;
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;   
   }

   public function GetOldSameLessons($LessonName)
   {
        $Result='';
        $Query = "SELECT * FROM lesson
        WHERE name  = ? ORDER BY id DESC LIMIT 1";
        $MyArray= array($LessonName);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute($MyArray);
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;  
   }
   
   public function GetOldSameUsers($FirstName,$LastName)
   {
        $Result='';
        $Query = "SELECT * FROM user WHERE first_name=? AND last_name =? ORDER BY id DESC LIMIT 1";
       // echo $Query;
        
        $MyArray= array($FirstName,$LastName);
       // print_r($MyArray);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute($MyArray);
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
       // print_r($Result);
        return $Result;   
   } 
   
   public function GetUserByURL($UURL)
   {
     $Result='';
        $Query = "SELECT * FROM user
        WHERE user_url = ?";
        $MyArray= array($UURL);    
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute($MyArray);
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;   
   }
   
   public function GetLessonUsingURL($LURL)
   {
        $Result='';
        $Query = "SELECT * FROM lesson
        WHERE lesson_url = ?";
        $MyArray= array($LURL);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute($MyArray);
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
//        print_r($Result);
//        exit;
        return $Result;   
   }
   
   public function GetOldResponseExists($UserID)
   {
        $Result='';
        $Query = "SELECT * FROM response_rate
        WHERE user_id = $UserID";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;   
   }
   
   public function AddUserRequestCount($UserID)
   {
        $Result='';
        $Query = "UPDATE response_rate
            SET total_request = total_request+1
        WHERE user_id = ?";
        $MyArray= array($UserID);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $Result = $MyPrepare->execute($MyArray);
        
        return $Result;   
   }
   
   public function InsertUserRequestCount($UserID)
   {
        $Result='';
        $Query = "INSERT INTO response_rate
            SET total_request = 1, user_id = ?";
        $MyArray= array($UserID);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $Result = $MyPrepare->execute($MyArray);
        // $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;   
   }
   
   public function AddResponseCountandTimeDifference($UserID,$TimeDifference)
   {
        $Result='';
        $Query = "UPDATE response_rate
        SET total_response = total_response+1,
        time_for_first_response = time_for_first_response+$TimeDifference
        WHERE user_id = ?";
        $MyArray= array($UserID);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $Result = $MyPrepare->execute($MyArray);
        return $Result;  
   }
   
   public function GetRequestDateTime($RequestID)
   {
        $Result='';
        $Query = "SELECT * FROM request_datetime
        WHERE request_id = ?";
        $MyArray= array($RequestID);
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute($MyArray);
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;    
   } 
   
   public function GetMessagebyInstructorCount($RequestID,$Instrtuctor)
   {
        $Result='';
        $Query = "SELECT count(*) as total FROM messages 
        WHERE req_id = $RequestID AND sender =$Instrtuctor";
       // $MyArray= array($RequestID,$Instrtuctor);
       // echo $Query;
        
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        //  echo "<pre>";print_r($Result);echo "</pre>";
        return $Result;  
   } 
   
   public function GetUserLessonRequests($UserID)
   {
        $Result='';
        $Query="SELECT r.*, RD.datetime as req_datetime, r.id AS rid
        FROM request as r INNER JOIN request_datetime as RD ON RD.request_id = r.id INNER JOIN lesson as l ON l.id = r.lessonid 
        WHERE l.userid = $UserID";
        $MyPrepare = $GLOBALS['LYDBConnect']->prepare($Query);
        $MyPrepare->execute();
        $Result = $MyPrepare->fetchAll(PDO::FETCH_ASSOC);
        return $Result;  
   }        
}
?>
