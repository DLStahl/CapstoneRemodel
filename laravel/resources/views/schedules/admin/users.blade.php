@extends('main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5>
        <hr>
        <form>
            <div class="form-group">
                <label name="email">Email:</label>
                <input id="email_usr" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label name="name">Name:</label>
                <input id="name_usr" name="name" class="form-control">
            </div>
            <div class="form-group">
                <label name="id">ID:</label>
                <input id="id_usr" name="id" class="form-control">
            </div>
            <input type="button" value="Add Admin" class="btn btn-success" id="Admin" onclick = "addUsers(this.id);">
            <input type="button" value="Add Attending" class="btn btn-success" id="Attending" onclick = "addUsers(this.id);">
            <input type="button" value="Add Resident" class="btn btn-success" id="Resident" onclick = "addUsers(this.id);">
        </form>
        </h5>
    </div>
    <script type="text/javascript">

    
        function addUsers(id)
        {
            if (document.getElementById('email_usr').value.indexOf("@osu.edu") == -1)
            {
                alert("Valid osu.edu email address is required");
                return;
            }
            if (document.getElementById('name_usr').value.length <= 0)
            {
                alert("Valid name is required");
                return;
            }

            var email = document.getElementById('email_usr').value;
            email = email.substr(0, email.indexOf(".edu")+4);
            var role = id;
            var name = document.getElementById('name_usr').value;
            if (role.indexOf("Attending") != -1) {
                if (document.getElementById('id_usr').value == "")
                {
                    alert("ID is required");
                    return;
                }
                name = name + "<" + document.getElementById('id_usr').value + ">";
            }
            if ((role.indexOf("Resident") != -1) && document.getElementById('id_usr').value != ""){
              name = name + "<" + document.getElementById('id_usr').value + ">";
            }
            // alert(email);

            // Update url to the confirmation page
            var current_url = window.location.href;
            var url = current_url.search('/addUser/') > -1 ? current_url.substr(0, current_url.search('/addUser/')) : current_url;
            url = url + "/addUser/" + role + "/" + email + "/false/" + name;
            window.location.href = url;
        }

    </script>
</div>

<br><br>
<div id="tb">
<table id="users_table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Operation</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roles as $role)
            <tr>
                <td align="left">{{ $role['name'] }}</td>
                <td align="left">{{ $role['email'] }}</td>
                <td align="left">{{ $role['role'] }}</td>
                <td id="button">
                    @if ($role['role'] != "Admin")
                        <input align="center" onclick = "deleteUsers(this.id);" value="Delete User" type="button" class='btn btn-md btn-success' id="{{ $role['email'] }}_{{ $role['role'] }}">
                    @else
                        <br>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>

        <script type="text/javascript">
            function deleteUsers(id)
            {
                var email = id.substr(0, id.indexOf('_'));
                var role = id.substr(id.indexOf('_')+1);

                // Update url to the confirmation page
                var current_url = window.location.href;
                var url = current_url.search('/deleteUser/') > -1 ? current_url.substr(0, current_url.search('/deleteUser/')) : current_url;
                url = url + "/deleteUser/" + role + "/" + email + "/false";
                window.location.href = url;
            }

        </script>
    </tr>
</table>
</div>


<style>
    table { 
      width: 100%; 
      border-collapse: collapse; 
    }
    /* Zebra striping */
    tr:nth-of-type(odd) { 
      background: #eee;
      border: 1px solid #ccc;
    }
    th { 
      background: white; 
      font-weight: bold; 
      text-align: center; 
      padding: 6px; 
      border: 1px solid #ccc; 
    }
    td{ 
      padding: 6px; 
      border: 1px solid #ccc; 
      text-align: left; 
      /*hyphens: auto;*/
    }
</style>
<!--[if !IE]><!-->
<style>
/* 
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {
    .btn{
        padding: .3rem .7rem;
        font-size: 14px;
    }

    /* Force table to not be like tables anymore */
    table, thead, tbody, th, td, tr { 
        display: block; 
    }
    
    /* Hide table headers (but not display: none;, for accessibility) */
    thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    tr { border: 1px solid #ccc; }
    
    td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 30%; 
    }

    tr:nth-of-type(odd) td:not(:last-of-type){
        border-bottom: 1px solid white; 
    }
    
    td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 5px; 
        white-space: nowrap;
    }
    
    /*
    Label the data
    */
    td:nth-of-type(1):before { content: "Name"; }
    td:nth-of-type(2):before { content: "Email"; }
    td:nth-of-type(3):before { content: "Role"; }
    td:nth-of-type(4):before { content: "Operation"; }
}

</style>

<!--<![endif]-->

@endsection
