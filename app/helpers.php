<?php
namespace Helpers;

function trythis()
{
	return "yep";
}

function replacegoogle($text)
{
	
	$fixed=preg_replace("/google\(([^\)]+)\)/", '<div><a href="https://docs.google.com/a/hamline.edu/document/d/$1">google doc</a><br/>
		<iframe src="https://docs.google.com/document/d/$1/pub?embedded=true" width="640" height="400"></iframe></div>',$text);
	
	
	return \Markdown::render($fixed);
}


