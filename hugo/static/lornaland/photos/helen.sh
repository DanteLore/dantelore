

# The local directory with the pictures in it
PICDIR="/home/lorna/public_html/photos/helen/";

# The directory on the server on which the pictures will be stored
OUTDIR="photos/helen/";

# The title of the page
PAGETITLE="Helen and Jade's Wedding (Featuring Hen Night!)";
# The title tag for the top of the page
TITLE="Logical Genetics - Helen and Jade's Wedding"

# The colour scheme to use {green | blue | gold | red}
SCHEME="red";

# The number of columns
MAXCOLS=3;

# Characters which must be unique to thumbnail files
THUMBCHARS="tn_";

# File type of pictures to be used
PICTYPE=".jpg";

# Image HTML setup
IMGSTART="<img border=\"0\" src=\"";
IMGEND="\"/>";

# Link HTML setup
LINKSTART="<a target=\"_new\" href=\"../../showimage.php?image=";
#LINKSTART="<a href=\"";
LINKMID="&colour=$SCHEME\">";
LINKEND="</a>"

# Caption HTML setup
CAPTIONSTART="<p>";
CAPTIONEND="</p>";


#------------------------------------------------------------#
#                 NO NEED TO EDIT BELOW!                     #
#------------------------------------------------------------#

# Start the HTML
echo "<html>"
echo "<head>"
echo "<link rel=\"StyleSheet\" href=\"https://logicalgenetics.com/pictures.css\" type=\"text/css\">"
echo "<title>$TITLE</title>"
echo "</head>"
echo ""
echo "<body class=\"gold\" border=\"0\">"
echo ""
echo "<center><table cellpadding=\"20px\" cellspacing=\"0px\" width=\"95%\"><tr><td align=\"center\">"
echo ""
echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">"
echo "  <tr>"
echo "    <td valign=\"bottom\">"
echo "      <img name=\"image0\" src=\"https://logicalgenetics.com/redsmall.gif\" alt=\"Logical Genetics Bloke\"/>"
echo ""
echo "    </td>"
echo "    <td valign=\"bottom\">"
echo "      <img name=\"image1\" src=\"https://logicalgenetics.com/bluesmall.gif\" alt=\"Logical Genetics Bloke\"/>"
echo "    </td>"
echo "    <td valign=\"bottom\">"
echo "      <img name=\"image2\" src=\"https://logicalgenetics.com/yellowsmall.gif\" alt=\"Logical Genetics Bloke\"/>"
echo "    </td>"
echo "    <td valign=\"bottom\">"
echo "      <img name=\"image3\" src=\"https://logicalgenetics.com/greensmall.gif\" alt=\"Logical Genetics Bloke\"/>"
echo ""
echo "    </td>"
echo "    <td style=\"text-align: right\">"
echo "      <a href=\"https://logicalgenetics.com/\"><img src=\"https://logicalgenetics.com/loggenbanner.gif\" alt=\"Logical Genetics\" border=\"0\"/></a>&nbsp;&nbsp;&nbsp;"
echo "      <a href=\"https://logicalgenetics.com/\"><img src=\"https://logicalgenetics.com/lg.gif\" alt=\"lg.gif\" border=\"0\"/></a>"
echo "    </td>"
echo "  </tr>"
echo "</table>"
echo ""
echo "<script language=\"JavaScript\" type=\"text/javascript\">"
echo "    <!-- Begin"
echo "    var cacheImage = new Image();"
echo "    cacheImage.src = \"experienceS.gif\";"
echo ""
echo ""
echo "    var dudes = new Array(4);"
echo ""
echo "    for(var a = 0; a < 4; a++) {"
echo "      dudes[a] = new Image();"
echo "    }"
echo ""
echo "    dudes[0].src = \"https://logicalgenetics.com/redsmall.gif\";"
echo "    dudes[1].src = \"https://logicalgenetics.com/bluesmall.gif\";"
echo "    dudes[2].src = \"https://logicalgenetics.com/yellowsmall.gif\";"
echo "    dudes[3].src = \"https://logicalgenetics.com/greensmall.gif\";"
echo ""
echo "    var targets = new Array(4);"
echo ""
echo "    targets[0] = image0;"
echo "    targets[1] = image1;"
echo "    targets[2] = image2;"
echo "    targets[3] = image3;"
echo ""
echo "    var i = Math.round(Math.random() * 3);"
echo "    var dir = Math.round(Math.random());"
echo ""
echo "    for(var j = 0; j < 4; j++) {"
echo "      if(i >= 3 && dir == 0) {"
echo "        i = 0;"
echo "      }"
echo "      else if(i <= 0 && dir == 1) {"
echo "        i = 3;"
echo "      }"
echo "      else if(dir == 0) {"
echo "        i = i + 1;"
echo "      }"
echo "      else if(dir == 1) {"
echo "        i = i - 1;"
echo "      }"
echo ""
echo "      targets[j].src = dudes[i].src;"
echo "    }"
echo "    // End -->"
echo "</script>"

# Start the table
echo "<table cellpadding=\"5\" width=\"100%\">"

echo "<h1>$PAGETITLE</h1>";

echo "  <tr>";
echo "    <td class=\"$SCHEME2\" colspan=\"$MAXCOLS\" valign=\"center\" align=\"center\">";
echo "      <p>Click a picture to see a larger version!</p>";
echo "    </td>";
echo "  </tr>";

echo "  <tr>";
echo "    <td class=\"$SCHEME2\" colspan=\"$MAXCOLS\" height=\"20\"></td>";
echo "  </tr>";    

# Output the thumbnail stuff 

COUNT=0
NUMPICS=0
STYLEID=2

# ls -t for sort by date
# ls -r to reverse order
# therefore ls -t -r for descending date order...

for A in `ls $PICDIR | grep $PICTYPE | grep -v $THUMBCHARS` 
do
  NUMPICS=$[ $NUMPICS + 1 ];

  if `test $COUNT -eq 0`
  then
    echo "  <tr>";
    COUNT=1;
  elif `test $COUNT -eq $MAXCOLS` 
  then
    echo "  <tr>";
    COUNT=1;
  else
    COUNT=$[ $COUNT + 1 ];
  fi

  if `test $STYLEID -eq 1`
  then
    STYLEID=2;
  else
    STYLEID=1;
  fi

  THUMBFILE=$THUMBCHARS$A
  BIGFILE=$OUTDIR$A
  echo "    <td class=\"$SCHEME$STYLEID\" valign=\"center\" align=\"center\">";
  echo "      $LINKSTART$BIGFILE$LINKMID$IMGSTART$THUMBFILE$IMGEND$LINKEND";
  echo "      $CAPTIONSTART";
  echo "        ";
  echo "      $CAPTIONEND";
  echo "    </td>";

  if `test $COUNT -eq $MAXCOLS`
  then
    echo "  </tr>";
  fi

done

if `test $COUNT -ne $MAXCOLS`
then
  echo "  </tr>";
fi


# end the table
echo "</table>";

# Output the number of pictures and some stats
echo;
echo "<br/>";
echo "<center>";
echo "  Generated by Dan's clever HTML generator.<br/>";
echo "  Number of pictures:  <strong>$NUMPICS</strong>";
echo "</center>";

# End the HTML
echo;
echo "</body>";
echo "</html>";
