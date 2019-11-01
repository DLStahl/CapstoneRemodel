@extends('main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5>
        <hr>
        <form>
            <div class="form-group">
                <font size="6">Add Milestone:</font>
                <br/>
                <br/>
                <label>Code:</label>
                <input id="code" name="newCode" class="form-control">
                <label>Category:</label>
                 <input id="Category" name="newCategory" class="form-control">
                 <label> Detail:</label>
                  <input id="details" name="newDetails" class="form-control">
            </div>
            <input type="button" value="Add" class="btn btn-success" id="Admin" onclick = "addMilestones();">
        </form>
        </h5>
    </div>
    <script type="text/javascript">
        function addMilestones()
        {
        
            var code = document.getElementById('code').value;
            var category = document.getElementById('category').value;
            var details = document.getElementById('details').value;

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
       <font size="9"> Milestones</font>
          <td align="left"> Code </td>
          <td align="left"> Category </td>
          <td align="left"> Detail </td>
          <td align="left">  </td>
          <td align="left"> </td>
          <td align="left"> </td>
        @foreach ($milestone as $mile)
            <tr>
           
                <td align="left"> <div contenteditable>{{ $mile['category'] }}</div></td>
                 <td align="left"><div contenteditable>{{ $mile['title'] }}</div></td>
                <td align="left"><div contenteditable>{{ $mile['detail'] }}</div></td>
    
               <td>
                        <input align="center" onclick = "saveMilestone($mile['id']);" value="Save" type="button" class='btn btn-md btn-success' id="{ $mile['category'] }">
                        </td>
                       
                </td>
                 <td>
                        <input align="center" onclick = "undoMilestone();" value="Undo" type="button" class='btn btn-md btn-success'">
                        </td>
                </td>
                  <td>
                        <input align="center" onclick = "deleteMilestone($mile['id']);" value="Delete" type="button" class='btn btn-md btn-success' id="{ $mile['category'] }">
                        </td>
                </td>
            </tr>
        @endforeach

        <script type="text/javascript">

            function deleteMilestone(id)
            {


                // Update url to the confirmation page
                var current_url = window.location.href;
                var url = current_url.search('/deleteUser/') > -1 ? current_url.substr(0, current_url.search('/deleteUser/')) : current_url;
                url = url + "/deleteUser/" + role + "/" + email + "/false";
                window.location.href = url;
            }

            function saveMilestone(id)
            {


                // Update url to the confirmation page
                var current_url = window.location.href;
                var url = current_url.search('/deleteUser/') > -1 ? current_url.substr(0, current_url.search('/deleteUser/')) : current_url;
                url = url + "/deleteUser/" + role + "/" + email + "/false";
                window.location.href = url;
            }

               function undoMilestone()
            {

                location.reload();
            }

        </script>
    </tr>
</table>
@endsection
