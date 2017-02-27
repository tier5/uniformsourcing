<?php
require('Application.php');
require('../header.php');
echo "<font face=\"arial\">";
echo "<center><font size=\"5\">SALES</font>";
echo "<p>";
echo "<b><a href=\"orderform.pdf\">Order Form</a><b> | ";
echo "<b><a href=\"sales_tracking.php\">Sales Tracking</a></b>";

echo '<p>';
echo "<font size=\"5\">PDF FILES</font>";
echo '<a target="_blank" href="Sample Request.pdf">Sample Request</a> | ';
echo '<a target="_blank" href="pdf order form1.pdf">Order Form</a> | ';
echo '<a target="_blank" href="PDF Product Form.pdf">Product Form</a> | ';

echo '<a target="_blank" href="pdf order form.pdf">Order Form 2</a><br>';

echo '<a target="_blank" href="PDF IMAGEWEAR ADD CLIENT EDIT.pdf">Imagewear Add Client Edit</a> | ';
echo '<a target="_blank" href="PDF Imagewear Embroidery Form.pdf">Imagewear Embroidery Form | </a>';
echo '<a target="_blank" href="Custom Material Sheet Editable.pdf">Custom Material Sheet | </a>';
echo '<a target="_blank" href="Return Authorization Form002.pdf">Return Authorization Form | </a>';
echo '<a target="_blank" href="Return Authorization Procedure002.docx">Return Authorization Procedure</a>';
require('../trailer.php');
?>