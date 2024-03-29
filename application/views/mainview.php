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

  <script id="jquery" type="text/javascript" src="<?php echo base_url(); ?>javascript/jquery-1.7.1.min.js"></script>
  <script id="loader" type="text/javascript" src="<?php echo base_url(); ?>javascript/ajaxloader.js"></script>
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
      <h1>Online Bunutan <span style="font-size: 10px">beta ver. 1.0</span></h1>
	  </header>

    <section id="get-started">
      <p style="line-height: 1.1;"><?php echo $section_desc; ?></p>
      <script type="text/javascript">
        $(document).ready(function () {
          var loading;
          $('input#submit').click(function () {
            loading = new ajaxLoader('div.loading');

            $.ajax({
              url: "<?php echo base_url(); ?>process",
              type: "POST",
              data: $('#process').serialize(),
              dataType: "json",
              cache: false,
              beforeSend: function () {
                  $('input.button').toggleClass('disabledbutton');
                  $('input.button').val("Maghintay Sandali...");
                  $('input.disabledbutton').attr('disabled', 'disabled');
              },
              success: function (ret) {
                // alert(ret.success);
                if(ret.success == 1) {
                  var src = "https://graph.facebook.com/"+ret.pick_id+"/picture?width=140&height=140";
                  var thumbimgsrc   = 'https://graph.facebook.com/'+ret.pick_id+'/picture?type=square';
                  $('img.anon').delay(2000).attr('src', src);
                  $('li.thumb<?php echo $user_id; ?> img').delay(2000).attr('src', thumbimgsrc);
                  $('li.thumb<?php echo $user_id; ?>  span.thumbname').delay(2000).html(ret.pick_name)
                  $('p.anon').delay(2000).html(ret.pick_name);
                  $('p#resultmsg').html("Ang Nabunot ni <?php echo he($user_name); ?> ay walang iba kundi si <br />" + ret.pick_name + ".");
                  $('input.button').fadeOut(10000);
                  $('input.button').hide();
                  $('p#resultmsg').delay(2000).show();
                }
                else {
                  alert("Sorry, unexpected error occured. Please try again later.\nResponse: "+ret.error_message)
                  $('input.button').removeClass('disabledbutton');
                  $('input.button').val("Pindutin");
                  $('input.button').removeAttr('disabled');
                }
              },
              error: function(ret, txtStatus, errorThrown) {
                alert(ret.error_message);
                $('input.button').val("Pindutin");
                $('input.disabledbutton').removeAttr('disabled');
              },
              complete: function(ret, txtStatus) {
                loading.remove();
              }
            });

            // cancel the submit button default behaviors
            return false;
          });
        });

      </script>
      <?php $attributes = array('id' => 'process'); ?>
      <?php echo form_open(base_url().'process', $attributes); ?>
        <div class="profile_pic">
          <div class="user_pic"><img src="<?php echo $user_image_url; ?>"></div>
          <p style="text-align: center; font-size: 14px;"><?php echo $user_name; ?></p>
        </div>
         <div class="unknown_pic">
            <div class="loading"><img src="<?php echo $pick_image_url; ?>" class="anon"></div>
            <p style="text-align: center; font-size: 14px;" class="anon"><?php echo $pick_name; ?></p>
            <?php echo form_hidden($form_hiddendata); ?>
          </div>
          <div class="clearfix"></div>
          <input type="submit" id="submit" class="button" value="Pindutin" <?php echo $hide; ?> />
          <p id="resultmsg" style="display: <?php echo $show; ?>; line-height: 1.1;"></p>
      <?php echo form_close(); ?>
    </section>

    <?php if($user_id == '1431783114' || $user_id == '559031999' || $user_id == '100001581655758'):?>
    <section id="samples" class="clearfix">
      <h1>Ang mga resulta</h1>
      <div class="list">
        <h3>Ang Bumunot</h3>
        <ul class="friends">
          <?php foreach ($members_pick as $member) : ?>
            <li>
              <a href="https://www.facebook.com/<?php echo $member['fb_id']; ?>" target="_top">
              <img src="<?php echo $member['fb_image_url_thumb']; ?>" alt="<?php echo he($member['fb_name']); ?>" />
              <span class="thumbname"><?php echo he($member['fb_name']); ?></span>
            </a>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="list mid">
        <h3 class="arrows">&nbsp;</h3>
        <ul class="arrows">
        <?php foreach($members_pick as $members) : ?>
          <li>&nbsp;</li>
        <?php endforeach; ?>
        <ul>
      </div>

      <div class="list">
        <h3>Nabunot ni bumunot</h3>
        <ul class="things">
          <?php foreach ($members_pick as $member) : ?>
            <li class="thumb<?php echo $member['fb_id']; ?>">
              <a href="https://www.facebook.com/<?php echo $member['pick_id']; ?>" target="_top">
              <img src="<?php echo $member['pick_image_url_thumb']; ?>" alt="<?php echo he($member['pick_name']); ?>" />
              <span class="thumbname"><?php echo he($member['pick_name']); ?></span>
            </a>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
    <?php endif; ?>
    <p style="text-align:center; margin-top: 40px; font-size: 11px;">Developed by <a href="http://www.facebook.com/mark.sargento" target="_top" style="font-weight: bold;">Iammac</a>. Hosted by <a href="http://heroku.com" target="_top">Heroku</a></p>
</body>
</html>
