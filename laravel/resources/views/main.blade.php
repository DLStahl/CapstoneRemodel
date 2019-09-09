<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>       
        <style>
            th {
                cursor: pointer;
            }

            ul.three{
                padding-inline-start:5px;
            }

        </style>
        @include('partials._header') 
    </head>
    <body>
        @include('partials._nav')
        <div class="container">
            @yield('content')   
            <br><br><br>
            <input align = "left" type="button" value="Return" id="return" class='btn btn-md btn-success' onclick="goBack();">
            <script>function goBack() { 
				if(window.location.pathname==("/laravel/public/admin/addDB"))
				{
					window.history.go(-2); 
				}
				else
				{
					window.history.back(); 
				}
			}
			</script>
            <script>
                if (window.location.pathname == "/" || window.location.pathname.includes("true"))
                {
                    document.getElementById("return").style.visibility = "hidden";
                }
            </script>       
        </div>

        @include('partials._footer')
        
    </body>
</html>
