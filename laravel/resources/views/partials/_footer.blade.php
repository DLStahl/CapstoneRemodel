<hr>
<div id="footer-wrap">
    <div id="footer">
        <div class="container">
            <div id="footer_left">
                <p>&copy; @php echo date('Y');@endphp OSU Anesthesiology</p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        if ((window.location.href.indexOf("secondday") > -1) || (window.location.href.indexOf("thirdday") > -
            1)) {
            $('#page_footer').css('position', 'fixed');
            $('#page_footer').css('bottom', '-15px');
            $('#page_footer').css('z-index', '5');
            adjustWidth();
        }
        $(window).resize(
            function() {
                adjustWidth();
            })

        function adjustWidth() {
            $("#page_footer").width(window.innerWidth);
        }
    });

</script>
