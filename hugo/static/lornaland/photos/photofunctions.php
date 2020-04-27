<?
  include_once("exif.php");

  $gallery_root_url = 'https://logicalgenetics.com/oldgallery/albums/';
  $gallery_root_dir = $site_base_dir . '/oldgallery/albums/';


  function get_pictures()
  {
    $sql = "select * from cpg_pictures";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    while($pic = mysql_fetch_object($result)) {
	    $pics[] = $pic;
    }

    return($pics);
  }

  function exif_info($pic)
  {
    global $exif_data;
    global $gallery_root_url;

    $filename = local_filename_for_pic($pic);


	if(file_exists($filename))
	{
      exif($filename);
      return($exif_data);
    }
    else
    {
	echo $filename;
      return(-1);
    }
  }

  function picture_taken_timestamp($pic)
  {
    $exif_data = exif_info($pic);

    if(!isset($exif_data["DateTime"]))
      return(-1);

    $str = $exif_data["DateTime"];

    $str = str_replace('-', ' ', $str);
    $str = str_replace(':', ' ', $str);
    $exp_date = explode(' ', $str);

    $t = mktime($exp_date[3], $exp_date[4], $exp_date[5], $exp_date[1], $exp_date[2], $exp_date[0]);

    return($t);
  }

  function next_picture($picture, $pics)
  {
    for($i = 0; $i < sizeof($pics) - 1; $i++)
    {
      if($pics[$i]->pid == $picture->pid)
      {
        return($pics[$i + 1]);
      }
    }

    return(0);
  }

  function prev_picture($picture, $pics)
  {
    for($i = 1; $i < sizeof($pics); $i++)
    {
      if($pics[$i]->pid == $picture->pid)
      {
        return($pics[$i - 1]);
      }
    }

    return(0);
  }

  function all_categories()
  {
    $sql = "select * from cpg_categories order by pos asc";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

	while($cat = mysql_fetch_object($result))
	{
	  $cats[] = $cat;
    }

    return($cats);
  }

  function category_by_id($cid)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_categories where cid = '$cid'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    return(mysql_fetch_object($result));
  }

  function albums_for_category($category)
  {
    $sql = "select * from cpg_albums where category = $category->cid order by pos asc";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

	while($album = mysql_fetch_object($result))
	{
	  $albums[] = $album;
    }

    return($albums);
  }

  function all_albums()
  {
    $sql = "select * from cpg_albums order by last_addition desc";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

	while($album = mysql_fetch_object($result))
	{
	  $albums[] = $album;
    }

    return($albums);
  }

  function album_by_id($aid)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_albums where aid = '$aid'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    return(mysql_fetch_object($result));
  }

  function pictures_for_album($album)
  {
    $sql = "select * from cpg_pictures where aid = $album->aid order by pid asc";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

	while($pic = mysql_fetch_object($result))
	{
	  $pics[] = $pic;
    }

    return($pics);
  }

  function picture_by_id($pid)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_pictures where pid = '$pid'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    return(mysql_fetch_object($result));
  }

  function url_for_pic($pic)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_pictures where pid = '$pic->pid'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    $pic = mysql_fetch_object($result);

    return($gallery_root_url . $pic->filepath . $pic->filename);
  }

  function local_filename_for_pic($pic)
  {
    global $gallery_root_dir;

    $sql = "select * from cpg_pictures where pid = '$pic->pid'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    $pic = mysql_fetch_object($result);

    return($gallery_root_dir . $pic->filepath . $pic->filename);
  }

  function thumb_url_for_pic($pic)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_pictures where pid = '$pic->pid'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    $pic = mysql_fetch_object($result);

    return($gallery_root_url . $pic->filepath . 'thumb_' . $pic->filename);
  }

  function thumb_url_for_album($album)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_pictures where pid = '$album->thumb'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    $pic = mysql_fetch_object($result);

    return($gallery_root_url . $pic->filepath . 'thumb_' . $pic->filename);
  }

  function thumb_url_for_category($category)
  {
    global $gallery_root_url;

    $sql = "select * from cpg_pictures where pid = '$category->thumb'";

    if ( !($result = mysql_query($sql)) )
	{
		echo 'Error Line: ' . __LINE__ . ' in ' . __FILE__ . ' SQL: "' . $sql . '"';
	}

    $pic = mysql_fetch_object($result);

    return($gallery_root_url . $pic->filepath . 'thumb_' . $pic->filename);
  }

  function connect_coppermine()
  {
    global $lgpassdata;

    $dbh = mysql_pconnect('localhost', 'loggen_snippets', 'snIppetspw') or die ('I cannot connect to the database because: ' . mysql_error());
    mysql_select_db("loggen_copp1");
    return($dbh);
  }

  function html_special_char_process($text)
  {
		$text = str_replace("&amp;","&", $text);
		$text = str_replace("&", "&amp;", $text);
		$text = str_replace("'", "&#180;", $text);
		$text = str_replace('"', "&quot;", $text);
		$text = str_replace("<", "&lt;", $text);
	    $text = str_replace(">", "&gt;", $text);

    return($text);
  }

?>