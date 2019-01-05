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
            if (!document.getElementById('email_usr').value.includes("@osu.edu"))
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
            if (role.includes("Attending")) {
                if (document.getElementById('id_usr').value == null)
                {
                    alert("ID is required");
                    return;
                }
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

<table class="table table-striped table-bordered" id="users_table">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Operations</th>
        @foreach ($roles as $role)
            <tr>
                <td align="left">{{ $role['name'] }}</td>
                <td align="left">{{ $role['email'] }}</td>
                <td align="left">{{ $role['role'] }}</td>
                <td>
                    @if ($role['role'] != "Admin")
                        <input align="center" onclick = "deleteUsers(this.id);" value="Delete User" type="button" class='btn btn-md btn-success' id="{{ $role['email'] }}_{{ $role['role'] }}">
                    @endif
                </td>
            </tr>                             
        @endforeach

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
@endsection