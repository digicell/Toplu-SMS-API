<?php

namespace DigicellSMSAPI;

class DigicellSMSAPI
{

	private static $username;
    private static $password;
    private static $header;


	public static function setUsername ($username) {
        self::$username = $username;
        return true;
    }


    public static function getUsername () {
        return self::$username;
    }


    public static function setPassword ($password) {
        self::$password = $password;
        return true;
    }

    public static function getPassword () {
        return self::$password;
    }

 
    public static function setHeader ($header) {
        self::$header = $header;
        return true;
    }

    public static function getHeader () {
        return self::$header;
    }

	public static function setConfig ($username, $password, $header) {
        self::$username = $username;
        self::$password = $password;
        self::$header = $header;
        return true;
    }
	
	public static function getConfig () {
        return array(self::$username, self::$password, self::$header);
    }
	
    /**
     * @param $url
     * @param $post_body
     * @return mixed
     *
     * Send request to server and get sms status
     *
     */
    private function send_server_response($url,$post_body=null){

        /**
         * Do not supply $post_fields directly as an argument to CURLOPT_POSTFIELDS,
         * despite what the PHP documentation suggests: cUrl will turn it into in a
         * multipart formpost, which is not supported:
         */

        $ch = curl_init( );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if($post_body){
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
		}
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml; charset=UTF-8"));
        $response_string = curl_exec( $ch );
        curl_close( $ch );

        return $response_string;

    }


    /**
     * @param $message
     * @param $number
     * @return mixed
     *
     * Send SMS Using API request
     */

    public function send_sms($message, $number){

		$username   = self::$username;
        $password   = self::$password;
        $header     = self::$header;

        $postData = "<sms>
					<username>".$username."</username>".
                        "<password>".$password."</password>".
                        "<header>".$header."</header>".
                       " <validity>2880</validity>".
						"<message>".
							"<msg><![CDATA[".$message."]]></msg>".
							"<gsm>";
				
				if(is_array($number)){
					foreach($number as $row){
						$postData.=	"<no>".$number."</no>";
					}
				}else{
					$postData.=	"<no>".$number."</no>";
				}
		
		
		
			$postData.=	"</gsm>".
						"</message>".
                   "</sms>";
	  
	  
		$postUrl = "http://api.sms.digicell.com.tr:8080/api/smspost/v1";

		
	  
        $response=$this->send_server_response($postUrl,$postData);

        return $response;

    }

    /**
     * @param $id
     * @return mixed
     *
     * DLR check
     *
     */

    public function iletimraporu($id){
		$username   = self::$username;
        $password   = self::$password;
        $url="http://api.sms.digicell.com.tr:8080/api/dlr/v1?username=".$username."&password=".$password."&id=".$id;

        $response=$this->send_server_response($url);
        return $response;


    }

    /**
     * @return mixed
     *
     * Get Balance for user
     *
     */

    public function bakiye(){
		$username   = self::$username;
        $password   = self::$password;
        $url="http://api.sms.digicell.com.tr:8080/api/credit/v1?username=".$username."&password=".$password;

        $response=$this->send_server_response($url);
        return $response;
    }


}