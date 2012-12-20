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
 
	$http = eZHTTPTool::instance();
	$variableArray = array();
	
	$prefix = eZSurveyType::PREFIX_ATTRIBUTE;
	$attributeID = $params[ 'contentobjectattribute_id' ];

	$postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
	
	$sAnswer = ''; //answer to save
	
	//check for network form field
	if( $http->hasPostVariable( $postSurveyAnswer ) )
	{				  
		$ini = eZINI::instance( 'bfsurveynetwork.ini' );			
		$server_addr = $_SERVER["REMOTE_ADDR"];     
			
		//TODO: perform ip to network lookup
		
		//tsting 
		$sAnswer = 'sanfrancisco';
	}
		 
     //SKIP: if( $this->attribute( 'mandatory' ) == 1 ) {}
 
     //Save
	 $this->setAnswer( $http->postVariable( $postSurveyAnswer, $sAnswer ) );
     $variableArray[ 'answer' ] = $http->postVariable( $postSurveyAnswer, $sAnswer );
 
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
	 
	 //option 2) check for answer post
    /*
     $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $this->contentObjectAttributeID();
     if ( $http->hasPostVariable( $postSurveyAnswer ) && strlen($http->postVariable( $postSurveyAnswer ) ) )
     {
         $surveyAnswer = $http->postVariable( $postSurveyAnswer );
         return $surveyAnswer;
     }   
     */
  }  
}
eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Network' ), 'Network' );
?>