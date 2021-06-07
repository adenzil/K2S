<?php

class K2S {

	protected $url;
	protected $email;
	protected $password;
	protected $auth_token;

	public function __construct($email, $password) {
		$this->email = $email;
		$this->password = $password;
		$this->url = "https://k2s.cc";
		$this->apiUrl = "https://api.k2s.cc";
		$this->login();
	}

	protected function initiateClient() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return $ch;
	}
	
	protected function login() {
		$ch = $this->initiateClient();
		echo "- Initiating login ... \n";
		curl_setopt($ch, CURLOPT_URL, $this->url . '/api/v2/login');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["username"=> $this->email, "password"=> $this->password]));
		$response = json_decode(curl_exec($ch), true);
		if($response['code'] == 200) {
			echo "   Login successful !!! \n\n";
			$this->auth_token = $response['auth_token'];
		} else {
			echo "   Opps ! Unable to login \n\n";
		}
		curl_close($ch);
	}

	protected function fetchUser() {
		$ch = $this->initiateClient();
		echo "- Fetching User ... \n";
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "/v1/users/me");
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			[
				"Content-Type: application/json" ,
				"Authorization: Bearer " . $this->auth_token
			]
		);
		$response = json_decode(curl_exec($ch), true);
		if(!empty($response)) {
			echo "   User fetch successful !!! \n\n";
			return $response;
		} else {
			echo "   Opps ! Unable to fetch user \n\n";
		}
		curl_close($ch);
	}

	protected function fetchUserStats() {
		$ch = $this->initiateClient();
		echo "- Fetching User Stats ... \n";
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "/v1/users/me/statistic");
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			[
				"Content-Type: application/json" ,
				"Authorization: Bearer " . $this->auth_token
			]
		);
		$response = json_decode(curl_exec($ch), true);
		if(!empty($response)) {
			echo "   User stats fetch successful !!! \n\n";
			return $response;
		} else {
			echo "   Opps ! Unable to fetch user stats \n\n";
		}
		curl_close($ch);
	}

	public function getProfileData() {
		$data = [];
		$data["Access Key"] = $this->auth_token;

		$user = $this->fetchUser();
		if($user) {
			$data["Account type"] = $user["accountType"];
		}

		$userStats = $this->fetchUserStats();
		if($userStats) {
			$data["Traffic left today for viewing/downloading"] = $userStats["dailyTraffic"]["total"] - $userStats["dailyTraffic"]["used"];
			$data["Used traffic today"] = $userStats["dailyTraffic"]["used"];
		}
		
		print_r($data);
		return $data;
	}

}