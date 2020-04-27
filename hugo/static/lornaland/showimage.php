<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
  <link rel=StyleSheet href="https://logicalgenetics.com/pictures.css" type="text/css"/>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
  <title>Logical Genetics</title>
</head>

<?

function caption_for_image($filename, $referer)
{
  $content = file($referer);

  $index = -1;

  for($i = 0; $i < sizeof($content); $i++)
  {
    if(strpos($content[$i], $filename) !== false)
    {
      // Found the filename.  Now find the caption...

      $index = $i + 2;

      break;
    }
  }

  if($index == -1)
    return("Untitled");

  $caption = $content[$index];
//  $caption = strip_tags($caption);
//  $caption = str_replace('\t', '', $caption);
//  $caption = str_replace('\n', '', $caption);


  return($caption);
}

function is_image_file($filename)
{
  return(
          (
            strpos(strtolower($filename), '.jpg') !== false ||
            strpos(strtolower($filename), '.gif') !== false
          )
          &&
          strpos(strtolower($filename), 'tn_') === false
        );
}

if(!isset($colour))
{
  $colour = "blue";
}

if(!isset($image))
{
  $image = "./lg.gif";
}

?>
<body class="<? echo $colour ?>">

<?

if(!isset($referer))
{
  $url = parse_url($HTTP_REFERER);

  $referer = $url["path"];

  while(!is_file($referer) && $referer != "")
  {
    $referer = substr($referer, strpos($referer, "/") + 1);
  }
}

$dirname = dirname($image);

$handle = opendir($dirname);
$i = 0;


while($file = readdir($handle))
{
  $fullname = $dirname.'/'.$file;

  if(is_file($fullname) && is_image_file($fullname))
  {
    $images[] = $fullname;
    $thumbs[] = $dirname.'/tn_'.$file;

    if($fullname == $image)
      $index = $i;

    $i++;
  }
}

$firstfile = $images[0];
$nextfile = $images[$index + 1];
$prevfile = $images[$index - 1];
$lastfile = $images[sizeof($images) - 1];

$nextthumb = $thumbs[$index + 1];
$prevthumb = $thumbs[$index - 1];
$nextnextthumb = $thumbs[$index + 2];
$prevprevthumb = $thumbs[$index - 2];

?>

<center>
  <table cellpadding="5" width="100%">
    <tr>
      <td class="<? echo $colour ?>1" valign="center" align="center" width="150px">

        <? if($prevprevthumb != "") { ?>
          <img height="50px" width="50px" alt="One before last" name="prevprevimage" src="<? echo $prevprevthumb ?>"/>
        <? } ?>

        <? if($prevthumb != "") { ?>
          <img height="75px" width="75px" alt="Previous" name="previmage" src="<? echo $prevthumb ?>"/>
        <? } ?>

        <br/>

        <? if($image != $firstfile) { ?>
        <a class="<? echo $colour ?>" href="<? echo $PHP_SELF.'?image='.$firstfile.'&colour='.$colour.'&referer='.$referer; ?>">&lt;&lt; First</a>&nbsp;&nbsp;
        <? } if($prevfile) { ?>
        <a class="<? echo $colour ?>" href="<? echo $PHP_SELF.'?image='.$prevfile.'&colour='.$colour.'&referer='.$referer; ?>">&lt; Prev</a>
        <? } ?>

      </td>
      <td class="<? echo $colour ?>2" valign="center" align="center">
        <h2><? echo caption_for_image($image, $referer) ?></h2>


      </td>
      <td class="<? echo $colour ?>1" valign="center" align="center" width="150px">

        <? if($nextthumb != "") { ?>
          <img height="75px" width="75px" alt="Next" name="nextimage" src="<? echo $nextthumb ?>"/>
        <? } ?>

        <? if($nextnextthumb != "") { ?>
          <img height="50px" width="50px" alt="One after next" name="nextnextimage" src="<? echo $nextnextthumb ?>"/>
        <? } ?>

        <br/>

        <? if($nextfile) { ?>
        <a class="<? echo $colour ?>" href="<? echo $PHP_SELF.'?image='.$nextfile.'&colour='.$colour.'&referer='.$referer; ?>">Next &gt;</a>&nbsp;&nbsp;
        <? } if($image != $lastfile) { ?>
        <a class="<? echo $colour ?>" href="<? echo $PHP_SELF.'?image='.$lastfile.'&colour='.$colour.'&referer='.$referer; ?>">Last &gt;&gt;</a>
        <? } ?>

      </td>
    </tr>
    <tr>
      <td colspan="3" class="<? echo $colour ?>1" valign="center" align="center">
        <img alt="The image you want to look at" name="theimage" src="<? echo $image ?>"/>
      </td>
    </tr>
  </table>
</center>

</body>

</html>