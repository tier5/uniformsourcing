

function gradient(id, level)
{
	var box = document.getElementById(id);
	box.style.opacity = level;
	box.style.MozOpacity = level;
	box.style.KhtmlOpacity = level;
	box.style.filter = "alpha(opacity=" + level * 200 + ")";
	box.style.display="block";
	return;
}


function fadein(id) 
{
	var level = 0;
	while(level <= 1)
	{
		setTimeout( "gradient('" + id + "'," + level + ")", (level* 1000) + 10);
		level += 0.01;
	}
}


// Open the lightbox


function openbox(boxname, formtitle, fadin)
{

  var box = document.getElementById(boxname); 
  document.getElementById('filter').style.display='block';

  var btitle = document.getElementById('boxtitle');
  btitle.innerHTML = formtitle;
  
  if(fadin)
  {
	 gradient(boxname, 0);
	 fadein(boxname);
  }
  else
  { 	
    box.style.display='block';
  }  	
}


// Close the lightbox

function closebox(boxname)
{
  document.getElementById(boxname).style.display='none';
   document.getElementById('filter').style.display='none';
}



