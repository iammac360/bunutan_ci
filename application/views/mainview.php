<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

	<title><?php echo he($app_name); ?></title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>stylesheets/screen.css" media="Screen" type="text/css" />
  <link rel="stylesheet" href="<?php echo base_url(); ?>stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />
  
  <!--[if IEMobile]>
  <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
  <![endif]-->

  <!-- These are Open Graph tags.  They add meta data to your  -->
  <!-- site that facebook uses when your content is shared     -->
  <!-- over facebook.  You should fill these tags in with      -->
  <!-- your data.  To learn more about Open Graph, visit       -->
  <!-- 'https://developers.facebook.com/docs/opengraph/'       -->

  <meta property="og:title" content="<?php echo he($meta['og:title']); ?>" />
  <meta property="og:type" content="<?php echo he($meta['og:description']); ?>" />
  <meta property="og:url" content="<?php echo $meta['og:url']; ?>" />
  <meta property="og:image" content="<?php echo $meta['og:image']; ?>" />
  <meta property="og:site_name" content="<?php echo $meta['og:title']; ?>" />
  <meta property="og:description" content="<?php echo $meta['og:description'] ?>" />
  <meta property="fb:app_id" content="<?php echo $meta['fb:app_id']; ?>" />

  <script type="text/javascript" src="<?php echo base_url(); ?>/javascript/jquery-1.7.1.min.js"></script>

  <script type="text/javascript">
    function logResponse(response) {
      if (console && console.log) {
        console.log('The response was', response);
      }
    }

    $(function(){
      // Set up so we handle click on the buttons
      $('#postToWall').click(function() {
        FB.ui(
          {
            method : 'feed',
            link   : $(this).attr('data-url')
          },
          function (response) {
            // If response is null the user canceled the dialog
            if (response != null) {
              logResponse(response);
            }
          }
        );
      });

      $('#sendToFriends').click(function() {
        FB.ui(
          {
            method : 'send',
            link   : $(this).attr('data-url')
          },
          function (response) {
            // If response is null the user canceled the dialog
            if (response != null) {
              logResponse(response);
            }
          }
        );
      });

      $('#sendRequest').click(function() {
        FB.ui(
          {
            method  : 'apprequests',
            message : $(this).attr('data-message')
          },
          function (response) {
            // If response is null the user canceled the dialog
            if (response != null) {
              logResponse(response);
            }
          }
        );
      });
    });
  </script>

  <!--[if IE]>
    <script type="text/javascript">
      var tags = ['header', 'section'];
      while(tags.length)
        document.createElement(tags.pop());
    </script>
  <![endif]-->
</head>
<body>
	<div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo $app_id; ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
    <header class="clearfix">
	<?php if (isset($user_info)): ?>
    <h1>Welcome</h1>
	<?php else: ?>	
    <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
	<?php endif; ?>
	</header>
</body>
</html>