﻿@extends('main')

@section('content')
    <form id="uploadFileForm" action="./milestones/uploadConfirm" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <h3> Upload milestones csv file </h3>
            <input type="file" class="form-control-file" name="fileUpload" id="InputFile">
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
    </form>
    <hr>

    <form id="addForm" method="POST" action="./milestones/add/false" onsubmit="return addMilestone();">
        <div class="form-group">
            <h3>Add a milestone:</h3>
            <label>Code:</label>
            <input id="code" name="newCode" class="form-control" required>
            <label>Category:</label>
            <input id="category" name="newCategory" class="form-control" required>
            <label> Detail:</label>
            <input id="detail" name="newDetail" class="form-control" required>
        </div>
        <input type="submit" value="Add" class="btn btn-success">
    </form>

<br><br>

<h3>Milestones</h3>
<table id="users_table">
    <thead>
        <tr>  
            <th> Code </th>
            <th> Category </th>
            <th> Detail </th>
            <th> Operation </th>
        </tr>
    </thead>
    <tbody>
        <?php $codes = array(); ?>
        @foreach ($milestone as $mile)
            <?php $codes[$mile['id']] =$mile['category'];?>
            <tr id="{{ $mile['id'] }}">
           
                <td align="left"> <div contenteditable="true" class="{{ $mile['id'] }} code">{{ $mile['category'] }}</div></td>
                <td align="left"><div contenteditable="true" class="{{ $mile['id'] }} category">{{ $mile['title'] }}</div></td>
                <td align="left"><div contenteditable="true" class="{{ $mile['id'] }} detail">{{ $mile['detail'] }}</div></td>

               <td class="b1">
                    <input align="center" onclick = "updateMilestone({{$mile['id']}});" value="Update" type="button" class="btn btn-md btn-success {{ $mile['id'] }} update" disabled="true">
                    <input align="right" onclick = "undoMilestone();" value="Undo" type="button" class="btn btn-md btn-success {{ $mile['id'] }} undo" disabled="true">
                    <input align="center" onclick = "deleteMilestone({{$mile['id']}});" value="Delete" type="button" class='btn btn-md btn-success'>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>

<script type="text/javascript">
    // Check a csv file is uploaded
    function fileValidation(){
        $('#uploadFileForm').validate({
            rules:{
                fileUpload: {
                    required: true,
                    extension: "xls|csv"
                }
            },
            messages: {
                fileUpload: "Please upload a .csv file",
            }
        });
    }
    fileValidation();
    $('#InputFile').change(function(){
        fileValidation();    
    });

    // enable "update" and "undo" buttons when user changes the content 
    $('[contenteditable="true"]').on('input', function(){
        var changeID = $(this).parent().parent()[0].id;
        console.log('.'+changeID+'.update');
        $('.'+changeID+'.update').prop('disabled', false);
        $('.'+changeID+'.undo').prop('disabled', false);
    });

    // get all existing abbr.
    var codes = <?php echo json_encode($codes); ?>;
    console.log(codes);

    function addMilestone()
    {
        var code = $('#code').val();
        if(Object.values(codes).includes(code)){
            alert("code "+ code + " already exists");
            return false;
        } else {
            return true;
        }
    }

    function deleteMilestone(id)
    {
        // Update url to the confirmation page (milestones/{op}/{flag}/{id}/{abbr_name?}/{full_name?}/{detail?})
        var current_url = window.location.href;
        var url = current_url + "/delete/false/" + id;
        window.location.href = url; 
    }

    function updateMilestone(id)
    {
        var code = $('.'+id+'.code')[0].innerText;
        var category = $('.'+id+'.category')[0].innerText;
        var detail = $('.'+id+'.detail')[0].innerText;
        // check if abbr is duplicated
        if(Object.values(codes).includes(code) && Object.keys(codes).find(key => codes[key] === code) != id){
            alert("code "+ code + " already exists");
            return;
        }
        // Update url to the confirmation page (milestones/{op}/{flag}/{id}/{abbr_name?}/{full_name?}/{detail?})
        var current_url = window.location.href;
        var url = current_url + "/update/false/" + id + "/" + code + "/" + category + "/" + detail;
        console.log(url);
        window.location.href = url;  

    }

    function undoMilestone()
    {
        location.reload();
    }

</script>
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

    .b1{
        white-space: nowrap
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
    td:nth-of-type(1):before { content: "Code"; }
    td:nth-of-type(2):before { content: "Category"; }
    td:nth-of-type(3):before { content: "Detail"; }
    td:nth-of-type(4):before { content: "Operation"; }
}

</style>
<!--<![endif]-->
@endsection
