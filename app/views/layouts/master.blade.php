<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
		<style>
			table form { margin-bottom: 0; }
			form ul { margin-left: 0; list-style: none; }
			.error { color: red; font-style: italic; }
			body { padding-top: 20px; }
			@yield('css')
		</style>
		<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
		@yield('head')
		
		<title>Course</title>
	</head>

	<body>
<div class="container">
<div>
{{link_to_action('UsersController@getDashboard', 'dashboard')}}
{{link_to_action('UsersController@getLogout', 'logout')}}
</div>

		
			
			@yield('main')
			
		</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
     <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
    @yield('head')
	</body>

</html>
