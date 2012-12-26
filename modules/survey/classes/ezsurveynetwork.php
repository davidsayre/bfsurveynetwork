<?php
class eZSurveyNetwork extends eZSurveyQuestion
{
 /*
   * constructor
   */
 function __construct( $row = false )
 {
    $row[ 'type' ] = 'Network';
    $this->eZSurveyQuestion( $row );
 }
 
 function eZSurveyNetwork( $row = false )
 {
	return self::__construct($row);
 }
 
  /*
     * called when a question is created / edited in the admin
     * In this case we only have to save the question text and the mandatory checkbox value
     */
  function processEditActions( &$validation, $params )
  {
      $http = eZHTTPTool::instance();
      $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
      $attributeID = $params[ 'contentobjectattribute_id' ];
 
      //title of the question
      $postQuestionText = $prefix . '_ezsurvey_question_' . $this->ID . '_text_' . $attributeID;
      if( $http->hasPostVariable( $postQuestionText ) and $http->postVariable( $postQuestionText ) != $this->Text )
      {
          $this->setAttribute( 'text', $http->postVariable( $postQuestionText ) );
      }
 
      $postQuestionMandatoryHidden = $prefix . '_ezsurvey_question_' . $this->ID . '_mandatory_hidden_' . $attributeID;
      if( $http->hasPostVariable( $postQuestionMandatoryHidden ) )
      {
          $postQuestionMandatory = $prefix . '_ezsurvey_question_' . $this->ID . '_mandatory_' . $attributeID;
          if( $http->hasPostVariable( $postQuestionMandatory ) )
              $newMandatory = 1;
          else
              $newMandatory = 0;
 
          if( $newMandatory != $this->Mandatory )
              $this->setAttribute( 'mandatory', $newMandatory );
      }
  }
 
  /*
    * Parse for network
	*/
  function processViewActions( &$validation, $params )
  {
 
  	//$http = eZHTTPTool::instance();
  	$variableArray = array();
  	
  	//$prefix = eZSurveyType::PREFIX_ATTRIBUTE;
  	//$attributeID = $params[ 'contentobjectattribute_id' ];

  	//$postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
  	
  	$sClientIP = self::getClientIPAddress();

    //Save answer from client ip (not post)
  	$this->setAnswer( $sClientIP );
    $variableArray[ 'answer' ] = $sClientIP;
 
    return $variableArray;
  }
 
  /*
   * called when a user answers a question on the public side
   */
  function answer()
  {
    //option 1) check for already defined    
    if ( strlen($this->Answer) ) {
      return $this->Answer;
    }

    //option 2) get values
    $sClientIP = self::getClientIPAddress();    
    $surveyAnswer = $sClientIP;

    $sNetworkLabel = false; //init
    
    //lookup network by client ip
    if(class_exists('cmsxAuthIP')) {
      $auth =  cmsxAuthIP::findByIP($sClientIP, false, false, false, true);

      if( isset( $auth[0] ) )
      {
        $aNetwork = array(); //init
        $aNetwork['user_id'] = $auth[0]['user_id'];
        $aNetwork['address'] = $auth[0]['address'];
        $aNetwork['user'] = eZUser::fetch( $auth[0]['user_id'] );
        if(array_key_exists('user_id',$aNetwork) && is_object($aNetwork['user'])) {
          $aNetwork['login'] = $aNetwork['user']->attribute('login');
          $sNetworkLabel = trim($aNetwork['login']); //copy label
        }       
      }    
    }

    //if network match found append to answer using '|'
    if($sNetworkLabel) {
      $surveyAnswer = trim($surveyAnswer) . "|" . $sNetworkLabel;
    }

    $this->setAnswer($surveyAnswer); //save
    return $surveyAnswer;
  }  

  /* copied from cmsxAuthIPTools */
  static function getClientIPAddress() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
      if (array_key_exists($key, $_SERVER)) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
          if (self::isValidIP($ip)) {
            return $ip;
          }
        }
      }
    }
    return null;
  }
  
  /* copied from cmsxAuthIPTools */
  public static function isValidIP( $ip )
  {
    if (function_exists('filter_var'))
    {
      return ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4|FILTER_FLAG_NO_RES_RANGE ) );
    } else {
      return self::depicatedIpCheck($ip);
    }
  }

  /* copied from cmsxAuthIPTools */
  static function depicatedIpCheck($ip)
  {

    $regexp = "([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})";
    
    $validate = ereg($regexp, $ip);
    
    if ($validate == true)
    {
        return true;
    }
    else
    {
        return false;
    }
  } 

}

eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Network' ), 'Network' );
?>