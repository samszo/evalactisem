<?php
   /***************************************************************/
   /* PhpDelicious - a library for accessing the del.ico.us API
   
      Software License Agreement (BSD License)
   
      Copyright (C) 2005-2008, Edward Eliot.
      All rights reserved.
      
      Redistribution and use in source and binary forms, with or without
      modification, are permitted provided that the following conditions are met:

         * Redistributions of source code must retain the above copyright
           notice, this list of conditions and the following disclaimer.
         * Redistributions in binary form must reproduce the above copyright
           notice, this list of conditions and the following disclaimer in the
           documentation and/or other materials provided with the distribution.
         * Neither the name of Edward Eliot nor the names of its contributors 
           may be used to endorse or promote products derived from this software 
           without specific prior written permission of Edward Eliot.

      THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS" AND ANY
      EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
      WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
      DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
      DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
      (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
      LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
      ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
      (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
      SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
   
      Last Updated: 20th January 2008  (see readme.txt)
                                                                  */
   /***************************************************************/
   
   // include required files
   require('xmlparser.inc.php');
   require('cache.inc.php');
   
   // Project Homepage
   define('PHP_DELICIOUS_PROJECT_HOMEPAGE', 'http://www.ejeliot.com/projects/php-delicious/');
   
   // del.icio.us API base URL
   define('PHP_DELICIOUS_BASE_URL', 'https://api.del.icio.us/v1/');
   define('PHP_DELICIOUS_JSON_URL', 'http://badges.del.icio.us/feeds/json/');
   define('PHP_DELICIOUS_JSON_URL_FEEDS', 'http://feeds.delicious.com/v2/json/');
   define('PHP_DELICIOUS_RSS_URL', 'http://feeds.delicious.com/v2/rss/');
   
   // del.icio.us requires a custom user agent string - standard ones are likely to result in you being blocked
   define('PHP_DELICIOUS_USER_AGENT', 'PhpDelicious v2.0 ('.PHP_DELICIOUS_PROJECT_HOMEPAGE.')');
   
   define('PHP_DELICIOUS_CONNECT_TIMEOUT', 5); // specified in seconds
   define('PHP_DELICIOUS_TRANSFER_TIMEOUT', 10); // specified in seconds, maximum time allowed for full transfer
   define('PHP_DELICIOUS_DNS_TIMEOUT', 86400); // specified in seconds (1 day)
   
   // error codes
   define('PHP_DELICIOUS_ERR_CONNECTION_FAILED', 1);
   define('PHP_DELICIOUS_ERR_INCORRECT_LOGIN', 2);
   define('PHP_DELICIOUS_ERR_THROTTLED', 3);
   define('PHP_DELICIOUS_ERR_XML_PARSE', 4);
   define('PHP_DELICIOUS_ERR_UNKNOWN', 5);
      
   class PhpDelicious {
        var $sUsername; // your del.icio.us username
        var $sPassword; // your del.icio.us password
        var  $iCacheTime; // the length of time in seconds to cache retrieved data
        var $oXmlParser; // the XML parser object used to process del.icio.us returned data
        var $iLastRequest = null;
        var $iLastError = 0;
      
      /************************ constructor ************************/
      
      public function __construct($sUsername, $sPassword, $iCacheTime = 10) {
         // assign parameters
         $this->sUsername = urlencode($sUsername);
         $this->sPassword = urlencode($sPassword);
         $this->iCacheTime = $iCacheTime;
         
         // create instance of XML parser class
           $this->oXmlParser = new XmlParser();
      }
  
      /************************ private methods ************************/
      
        function FromDeliciousDate($sDate) {
         return trim(str_replace(array('T', 'Z'), ' ', $sDate));
      }
      
        function ToDeliciousDate($sDate) {
         return date('Y-m-d\TH:i:s\Z', strtotime($sDate));
      }
      
        function GetBoolReturn($sInput) {
         return ($sInput == 'done' || $sInput == 'ok');
      }
      
        function Delay() {
         // could use microtime but not supported on all systems
         if (!is_null($this->iLastRequest) && time() - $this->iLastRequest < 1) {
            sleep(1);
         } else {
            $this->iLastRequest = time();
         }
      }
      
        function HttpRequest($sCmd) {
         // check for curl lib, use in preference to file_get_contents if available
         if (function_exists('curl_init')) {
            // initiate session
            $oCurl = curl_init($sCmd);
            // set options
            curl_setopt_array($oCurl, array(
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_USERAGENT => PHP_DELICIOUS_USER_AGENT,
               CURLOPT_CONNECTTIMEOUT => PHP_DELICIOUS_CONNECT_TIMEOUT,
               CURLOPT_TIMEOUT => PHP_DELICIOUS_TRANSFER_TIMEOUT,
               CURLOPT_DNS_CACHE_TIMEOUT => PHP_DELICIOUS_DNS_TIMEOUT,
               CURLOPT_USERPWD => "$this->sUsername:$this->sPassword"
            ));
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            // request URL
            if ($sResult = curl_exec($oCurl)) {
               switch (curl_getinfo($oCurl, CURLINFO_HTTP_CODE)) {
                  case 200:
   	               return $sResult;
                     break;
                  case 503:
                     $this->iLastError = PHP_DELICIOUS_ERR_THROTTLED;
                     break;
                  case 401:
                     $this->iLastError = PHP_DELICIOUS_ERR_INCORRECT_LOGIN;
                     break;
                  default:
                     $this->iLastError = PHP_DELICIOUS_ERR_CONNECTION_FAILED;
               }
            }
            // close session
            curl_close($oCurl);
            
            return false;
         } else {
            // set user agent
            ini_set('user_agent', PHP_DELICIOUS_USER_AGENT);
             
            // add basic auth details
            $sCmd = str_replace('https://', "https://$this->sUsername:$this->sPassword@", $sCmd);
            
            // fopen_wrappers need to be enabled for this to work - see http://www.php.net/manual/en/function.file-get-contents.php
            if ($sResult = @file_get_contents($sCmd)) {
               if (strstr($http_response_header[0], '503')) {
                  $this->iLastError = PHP_DELICIOUS_ERR_THROTTLED;
               } else {
                  if (strstr($http_response_header[0], '401')) {
                     $this->iLastError = PHP_DELICIOUS_ERR_INCORRECT_LOGIN;
                  } else {
                      $sResult;
                  } 
               }
            } else {
               $this->iLastError = PHP_DELICIOUS_ERR_CONNECTION_FAILED;
            }
         }
         return false;
      }
      
       function DeliciousRequest($sCmd, $aParameters = array()) {
         if ($this->LastError() >= 1 && $this->LastError() <= 3) {
            return false;
         }
         
         // reset the last error
         $this->iLastError = 0;
         
         // construct URL - add username, password and command to run
         $sCmd = PHP_DELICIOUS_BASE_URL.$sCmd;
         
         // check for parameters
         if (count($aParameters) > 0) {
            $sCmd .= '?';
         }
         
         // add parameters to command
         $iCount = 0;
         
         foreach ($aParameters as $sKey => $sValue) {
            if ($sValue != '') {
               if ($iCount > 0) {
                  $sCmd .= '&';
               }
               $sCmd .= "$sKey=".urlencode($sValue);
               $iCount++;
            }
         }
         
         if ($sXml = $this->HttpRequest($sCmd)) {	
           // return result passed as array
            if ($aXml = $this->oXmlParser->Parse($sXml)) {
               return $aXml;
            } else {
               $this->iLastError = ERR_XML_PARSE;
            }
            
         }  
         return false;
      }
      
      // generic function to get post listings
        function GetList($sCmd, $sTag = '', $sDate = '', $sUrl = '', $iCount = -1) {
         $oCache = new Cache($this->sUsername."_".$sCmd."_".$sTag."_".$sDate."_".$sUrl."_".$iCount, $this->iCacheTime);
         
         if (!$oCache->Check()) {
            if ($sCmd == 'posts/all' && $oCache->Exists()) {
               $sLastUpdate = $this->GetLastUpdate();
                  
               $aData = $oCache->Get();
                  
               if ($aData['last-update'] == $sLastUpdate) {
                  $oCache->Set($aData);
                  return $aData['items'];
               }
            }
            
            // initialise parameters array
            $aParameters = array();
         
            // check for optional parameters
            if ($sTag != '') {
               $aParameters['tag'] = $sTag;
            }
            if ($sDate != '') {
               $aParameters['dt'] = $this->ToDeliciousDate($sDate);
            }
            if ($sUrl != '') {
               $aParameters['url'] = $sUrl;
            }
            if ($iCount != -1) {
               $aParameters['count'] = $iCount;
            }
         
            // make request
            if ($aResult = $this->DeliciousRequest($sCmd, $aParameters)) {
               $aPosts = array();
               $aPosts['last-update'] = $this->FromDeliciousDate($aResult['attributes']['UPDATE']);
               $aPosts['items'] = array();
               foreach ($aResult['items'] as $aCurPost) {
                  // check absence of tags for current URL
                  $aCurPost['attributes']['TAG'] != 'system:unfiled' ? $aTags = explode(' ', $aCurPost['attributes']['TAG']) : $aTags = array();
                  
                  $aNewPost = array(
                     'url' => $aCurPost['attributes']['HREF'],
                     'desc' => $aCurPost['attributes']['DESCRIPTION'],
                     'notes' => $aCurPost['attributes']['EXTENDED'],
                     'hash' => $aCurPost['attributes']['HASH'],
                     'tags' => $aTags,
                     'updated' => $this->FromDeliciousDate($aCurPost['attributes']['TIME'])
                  );
                  
                  if ($sCmd == 'posts/get') {
                     $aNewPost['count'] = $aCurPost['attributes']['OTHERS'];
                  }
                  
                  $aPosts['items'][] = $aNewPost;
               }
               $oCache->Set($aPosts);
            } else {
               $oCache->Set(false);
            }
         }
         $aData = $oCache->Get();
         return $aData['items'];
      }
      
      /************************ public methods ************************/
      
      public function LastError() { // alias to LastErrorNo for backwards compatibility
         return $this->LastErrorNo();
      }
      
      public function LastErrorNo() {
         return $this->iLastError;
      }
      
      public function LastErrorString() {
         switch ($this->iLastError) {
            case 1:
               return 'Connection to del.icio.us failed.';
            case 2:
               return 'Incorrect del.icio.us username or password.';
            case 3:
               return 'Del.icio.us API access throttled.';
            case 4:
               return 'XML parse error has occurred.';
            case 5:
               return 'An unknown error has occurred.';
            default:
               return '';
         }
      }
      
      public function GetLastUpdate() {
         // get last time the user updated their del.icio.us account
         if ($aResult = $this->DeliciousRequest('posts/update')) {        
            return $this->FromDeliciousDate($aResult['attributes']['TIME']);
         }  
         return false;
      }
      
 public function GetAllTags() {
         $oCache = new Cache('tags/arr_'.$this->sUsername, $this->iCacheTime);
         if (!$oCache->Check()) {
            if ($aResult = $this->DeliciousRequest('tags/get')) {
               $aTags = array();           
               foreach ($aResult['items'] as $aTag) {
                  $aTags[] = array(
                     'tag' => $aTag['attributes']['TAG'],
                     'count' => $aTag['attributes']['COUNT']
                  );
               }
               $oCache->Set($aTags);
            } else {    
               $oCache->Set(false);
            }
         }
         return $oCache->Get();
      }
    public function isUpdatePost() {
         $oCache = new Cache($this->sUsername.'_update', $this->iCacheTime);
         if (!$oCache->Check()) {
         	$aResult = $this->DeliciousRequest('posts/update');
            $oCache->Set($aResult['attributes']['TIME']);
         }
         $TimeCache=$this->GetFormatDate($oCache->Get());
         $aResult = $this->DeliciousRequest('posts/update');
         $TimeDelIcioiUs=$this->GetFormatDate($aResult['attributes']['TIME']);
       
        return $this->compArr($TimeDelIcioiUs,$TimeCache);
        
      }
      public function RenameTag($sOld, $sNew) {
         $this->Delay(); 
         
         if ($aResult = $this->DeliciousRequest('tags/rename', array('old' => $sOld, 'new' => $sNew))) {
            if ($aResult['content'] == 'done') {
               return true;
            }
         }
         return false;
      }
      
      public function GetPosts(
         $sTag = '', // filter by tag
         $sDate = '', // filter by date - format YYYY-MM-DD HH:MM:SS
         $sUrl = '' // filter by URL
      ) {
         return $this->GetList('posts/get', $sTag, $sDate, $sUrl);
      }
      
      public function GetRecentPosts(
         $sTag = '', // filter by tag
         $iCount = 15 // number of posts to retrieve, min 15, max 100
      ) {
         return $this->GetList('posts/recent', $sTag, '', '', $iCount);
      }
      
      public function GetAllPosts(
         $sTag = '' // filter by tag
      ) { 
         return $this->GetList('posts/all', $sTag, '', '', -1);
      }
      
      public function GetDates(
         $sTag = '' // filter by tag
      ) {
         // set up cache object
         $oCache = new Cache("posts/".$this->sUsername."_dates_".$sTag, $this->iCacheTime);
         
         // check for cached data
         if (!$oCache->Check()) {
            // return number of posts for each date
            if ($aResult = $this->DeliciousRequest('posts/dates', array('tag' => $sTag))) {
               $aDates = array();
            
               foreach ($aResult['items'] as $aCurDate) {
                  $aDates[] = array(
                     'date' => $this->FromDeliciousDate($aCurDate['attributes']['DATE']),
                     'count' => $aCurDate['attributes']['COUNT']
                  );
               }
               $oCache->Set($aDates);
            } else {
               $oCache->Set(false);
            }
         }
         // return data from cache
         return $oCache->Get();
      }
      
      public function AddPost(
         $sUrl, // URL of post
         $sDescription, // description of post
         $sNotes = '', // additional notes relating to post
         $aTags = array(), // tags to assign to the post
         $sDate = '', // date of the post, format YYYY-MM-DD HH:MM:SS - default is current date and time
         $bReplace = true // if set, any existing post with the same URL will be replaced
      ) {
         $this->Delay();
         
         $aParameters = array(
            'url' => $sUrl,
            'description' => $sDescription,
            'extended' => $sNotes,
            'tags' => implode(' ', $aTags)
         );
         
         if ($sDate != '') {
            $aParameters['dt'] = $this->ToDeliciousDate($sDate);
         }
         if (!$bReplace) {
            $aParameters['replace'] = 'no';
         }
      
         if ($aResult = $this->DeliciousRequest('posts/add', $aParameters)) {
            return $this->GetBoolReturn($aResult['attributes']['CODE']);
         }
      
         return false;
      }
      
      public function DeletePost($sUrl) {
         $this->Delay();
         
         if ($aResult = $this->DeliciousRequest('posts/delete', array('url' => $sUrl))) {
            return $this->GetBoolReturn($aResult['attributes']['CODE']);
         } 
         return false;
      }
      
      public function GetAllBundles() {
         $oCache = new Cache('tags/'.$this->sUsername.'_bundles_all', $this->iCacheTime);
         
         if (!$oCache->Check()) {
            if ($aResult = $this->DeliciousRequest('tags/bundles/all')) {
               $aBundles = array();
               foreach ($aResult['items'] as $aCurBundle) {
                  $aBundles[] = array(
                     'name' => $aCurBundle['attributes']['NAME'],
                     'tags' => $aCurBundle['attributes']['TAGS']
                  );
               }
               $oCache->Set($aBundles);
            } else {
               $oCache->Set(false);
            }
         }
         return $oCache->Get();
      }
      
      public function AddBundle($sName, $aTags) {
         $this->Delay();
         
         if ($aResult = $this->DeliciousRequest('tags/bundles/set', array('bundle' => $sName, 'tags' => implode(' ', $aTags)))) {
            return $this->GetBoolReturn($aResult['content']);
         } 
         return false;
      }
      
      public function DeleteBundle($sName) {
         $this->Delay();
         
         if ($aResult = $this->DeliciousRequest('tags/bundles/delete', array('bundle' => $sName))) {
            return $this->GetBoolReturn($aResult['content']);
         }
         return false;
      }
      
      // the remaining methods call the JSON API
      
      public function GetUrlDetails(
         $vUrls // this can take a single URL or an array of URLs (up to 15)
      ) {
         if (function_exists('json_decode')) {
            $oCache = new Cache('url/data'.implode($vUrls), $this->iCacheTime);
            
            if (!$oCache->Check()) {
               $sUrl = PHP_DELICIOUS_JSON_URL.'url/data?';
            
               if (is_array($vUrls)) {
                  foreach ($vUrls as $sCurrentUrl) {
                     $sUrl .= 'hash='.md5($sCurrentUrl).'&';
                  }
                  $sUrl .= rtrim($sUrl, '&');
               } else {
                  $sUrl .= 'hash='.md5($vUrls);
               }
            
               if ($sJson = $this->HttpRequest($sUrl)) {
                  $oCache->Set(json_decode($sJson));
               } else {
                  $oCache->Set(false);
               }
            }
            
            return $oCache->Get();
         }
         return false;
      }
      
      public function GetNetwork($sUsername) {
         if (function_exists('json_decode')) {
            $oCache = new Cache("network/$sUsername", $this->iCacheTime);
            
            if (!$oCache->Check()) {
               if ($sJson = $this->HttpRequest(PHP_DELICIOUS_JSON_URL."network/$sUsername")) {
                  $oCache->Set(json_decode($sJson));
               } else {
                  $oCache->Set(false);
               }
            }
            
            return $oCache->Get();
         }
         return false;
      }      
      
      public function GetMyNetwork() {
         return $this->GetNetwork($this->sUsername);
      }
      
      public function GetNetworkMembers($sUsername) {
            $oCache = new Cache("network/$sUsername", $this->iCacheTime);
            
            if (!$oCache->Check()) {
               if ($sRss = $this->HttpRequest(PHP_DELICIOUS_RSS_URL."networkmembers/$sUsername")) {
                  $oCache->Set($sRss,true);
               } else {
                  $oCache->Set(false,true);
               }
            }
            
            return $oCache->Get(true);
      }
      
      public function GetFans($sUsername) {
         if (function_exists('json_decode')) {
            $oCache = new Cache("fans/$sUsername", $this->iCacheTime);
            
            if (!$oCache->Check()) {
               if ($sJson = $this->HttpRequest(PHP_DELICIOUS_JSON_URL."fans/$sUsername")) {
                  $oCache->Set(json_decode($sJson));
               } else {
                  $oCache->Set(false);
               }
            }
            
            return $oCache->Get();
         }
         return false;
      }

      public function GetMyFans() {
         return $this->GetFans($this->sUsername);
      }

      public function GetUserTags($sUsername) {
            $oCache = new Cache("tags/$sUsername", $this->iCacheTime);
            if (!$oCache->Check()) {
               if ($sRss = $this->HttpRequest(PHP_DELICIOUS_RSS_URL."tags/$sUsername")) {
                  $oCache->Set($sRss,true);
               } else {
                  $oCache->Set(false,true);
               }
            }
            
            return $oCache->Get(true);
      }
      
      public function GetUserPosts($sUsername,$tag) {
            $oCache = new Cache("posts/".$sUsername."_".$tag, $this->iCacheTime);
            if (!$oCache->Check()) {
               if ($sRss = $this->HttpRequest(PHP_DELICIOUS_RSS_URL."/$sUsername/$tag?count=100")) {
                  $oCache->Set($sRss,true);
               } else {
                  $oCache->Set(false,true);
               }
            }
            return $oCache->Get(true);
      }
      
      public function GetUserBookmark($sUsername) {
            $oCache = new Cache("bookmarks/".$sUsername, $this->iCacheTime);   
            if (!$oCache->Check()) {
               if ($sRss = $this->HttpRequest(PHP_DELICIOUS_RSS_URL."/$sUsername?count=100")) {
                  $oCache->Set($sRss,true);
               } else {
                  $oCache->Set(false,true);
               }
            }
            
            return $oCache->Get(true);
      }
     public function GetFormatDate($String) {
           $date=explode('T',$String);
           $ArrDate=explode('-',$date[0]);
           $ArrHourMin=explode(':',$date[1]);
           array_push($ArrDate,$ArrHourMin[0],$ArrHourMin[1]);
           return $ArrDate;
      }
    public function compArr($Arr1,$Arr2){
    	for($i=0; $i<=2;$i++){
    		if($Arr1[$i]>$Arr2[$i])
    			$sup=true; 
    		else{
    		    $sup=false;
    		}
    	}
    	for($i=3; $i<=4;$i++){
    		if($Arr1[$i]>$Arr2[$i])
    			$sup=true; 
        	else{
        		$sup=false;
        	}
    		
    	}
    	return $sup;
    }
   }
?>