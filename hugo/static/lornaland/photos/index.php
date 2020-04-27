<?
  include_once('photofunctions.php');

  connect_coppermine();

  $album = album_by_id($_GET["aid"]);
  $category = category_by_id($album->category);
  $pics = pictures_for_album($album);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="https://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
  <link rel="StyleSheet" href="../lornaland.css" type="text/css"/>
  <title>lornaland</title>
  <style>
    td.main
    {
	  padding-left: 5%;
      padding-right: 5%;
    }
    #albumtable
    {
      width: 100%;
    }
    #albumtable td
    {
      padding: 0;
      margin: 0;
      vertical-align: top;
    }
    img.gallery
    {
      margin: 2px;
    }
  </style>
</head>

<body>

<center>
<table width="745px">
  <tr>
    <td class="picbox"><img class="header" src="../pics/501.jpg"/><img class="header" src="../pics/502.jpg"/><img class="header" src="../pics/503.jpg"/><img class="header" src="../pics/504.jpg"/><img class="header" src="../pics/505.jpg"/><img class="header" src="../pics/506.jpg"/><img class="header" src="../pics/507.jpg"/></td>
  </tr>

  <tr>
    <td class="main">


  <h1><? echo $album->title; ?></h1>

  <hr/>
  <p>
    Click on a picture for a larger version.
    <a class="menu" href="https://lornaland.co.uk/photogallery.html">Click here to return to the gallery</a>.
  </p>
  <hr/>

 <table id="albumtable">

<?
  for($i = 0; $i < sizeof($pics); $i++)
  {
    if($i % 3 == 0)
    {
?>
    <tr>
<?
    }
?>
    <td class="<? echo ($i % 2 == 0) ? 'piccell1' : 'piccell2' ?>" align="center">
      <a href="<? echo url_for_pic($pics[$i]); ?>"><img class="gallery" alt="<? echo html_special_char_process($pics[$i]->title); ?>" title="<? echo html_special_char_process($pics[$i]->title); ?>" src="<? echo thumb_url_for_pic($pics[$i]); ?>"/></a>
      <p><? echo html_special_char_process($pics[$i]->title); ?></p>
      <p><? echo html_special_char_process($pics[$i]->caption); ?></p>
    </td>
<?
    if(($i + 1) % 3 == 0)
    {
?>
    </tr>
<?
    }
  }

  if(sizeof($pics) % 3 != 0)
  {
?>
    </tr>
<?
  }
?>
  </table>



    </td>
  </tr>
</table>
</center>
</body>

</html>