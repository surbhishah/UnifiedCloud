<?php

class UsersController extends BaseController{
	public $restful = true;
	
	public function getHome(){

		return View::make('home');
	}

	public function getRegistrationPage(){
		// Redirect user to app authentication page of dropbox
		// uri of authentication is to be obtained using object of WebAuth class 
		$webauth =$this->getWebAuth(); 
		$authorizeUrl = $webauth->start();
		return Redirect::to($authorizeUrl);
	}
	

	private function getWebAuth(){
		session_start();
		
		$path= app_path().'/database/dropbox-app-info.json';
		$appInfo = Dropbox\AppInfo::loadFromJsonFile($path);
   	    $clientIdentifier = "Project-Kumo";
   	    $redirectUri = "http://localhost/UnifiedCloud/public/auth/dropbox";// This needs a Https link ..only localhost 
   	    															//is allowed for http
	    $csrfTokenStore = new Dropbox\ArrayEntryStore($_SESSION, 'date(format)ropbox-auth-csrf-token');
	    return new Dropbox\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
	}


	function getCompletion(){
		try {
				
			// Get access token of the user now he has authenticated our app

			//$_GET is an array of variables passed to the current script via the URL parameters.
				
				list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($_GET);
//			    assert($urlState === null);  // Since we didn't pass anything in start()
				$path =  app_path().'/accessToken.txt';
				File::put($path, $accessToken);		
				return View::make('complete');
			}

			catch (Dropbox\WebAuthException_BadRequest $ex) {
			   error_log("/dropbox-auth-finish: bad request: " . $ex->getMessage());
			   return View::make('user.error')
			   				->with('message',$ex->getMessage());
			   // Respond with an HTTP 400 and display error page...
			}

			catch (Dropbox\WebAuthException_BadState $ex) {
			   // Auth session expired.  Restart the auth process.
			   header('Location: /dropbox-auth-start');
			}

			catch (Dropbox\WebAuthException_Csrf $ex) {
			   error_log("/dropbox-auth-finish: CSRF mismatch: " . $ex->getMessage());
			   return View::make('user.error')
			   				->with('message',$ex->getMessage());
			   // Respond with HTTP 403 and display error page...
			}

			catch (Dropbox\WebAuthException_NotApproved $ex) {
			   error_log("/dropbox-auth-finish: not approved: " . $ex->getMessage());
			   return View::make('user.error')
			   				->with('message',$ex->getMessage());
			}
			
			catch (Dropbox\WebAuthException_Provider $ex) {
			   error_log("/dropbox-auth-finish: error redirect from Dropbox: " . $ex->getMessage());
			   return View::make('user.error')
			   				->with('message',$ex->getMessage());
			}
			
			catch (Dropbox\Exception $ex) {
			   error_log("/dropbox-auth-finish: error communicating with Dropbox API: " . $ex->getMessage());
			   return View::make('user.error')
			   				->with('message',$ex->getMessage());
			}
	}

	
}
