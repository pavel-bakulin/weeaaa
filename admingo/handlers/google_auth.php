<html>
<head>
  <title>Google+ Sign-in button demo: rendering with JavaScript</title>
  <style type="text/css">
  html, body { margin: 0; padding:0;}
  #signin-button {
   padding: 5px;
  }
  #oauth2-results pre { margin: 0; padding:0; width: 600px;}
  .hide { display: none;}
  .show { display: block;}
  </style>

  <script src="https://apis.google.com/js/client:plus.js" type="text/javascript"></script>
  <script type="text/javascript">
var loginFinished = function(authResult)
{
  if (authResult)
  {
    console.log(authResult);
  }

  gapi.client.load('oauth2', 'v2', function()
  {
    gapi.client.oauth2.userinfo.get({'userId':'me'})
      .execute(function(resp)
      {
        // Shows user email
        console.log(resp);
      });
  });

};

  </script>

</head>
  <div id="signin-button" class="show">
     <div class="g-signin" data-callback="loginFinished"
      data-approvalprompt="force"
      data-clientid="384688347328-11q6u4to9f150cnq480vldpcaj08v43o.apps.googleusercontent.com"
      data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me"
      data-height="short"
      data-cookiepolicy="single_host_origin"
      >
    </div>
  </div>
</html>