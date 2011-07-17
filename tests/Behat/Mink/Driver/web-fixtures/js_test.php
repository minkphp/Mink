<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <title>JS elements test</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
    <div class="elements">
        <div id="clicker">not clicked</div>
        <div id="invisible" style="display: none">invisible man</div>
        <input class="input first" type="text" value="" />
        <input class="input second" type="text" value="" />
        <input class="input third" type="text" value="" />
        <div class="text-event"></div>
    </div>

    <script language="javascript" type="text/javascript" src="/js/jquery-1.6.2-min.js"></script>
	<script language="javascript" type="text/javascript">
		$(document).ready(function() {
            $('#clicker').click(function() {
                $(this).text('single clicked');
            });

            $('#clicker').dblclick(function() {
                $(this).text('double clicked');
            });

            $('#clicker').bind('contextmenu', function() {
                $(this).text('right clicked');
            });

            $('#clicker').focus(function() {
                $(this).text('focused');
            });

            $('#clicker').blur(function() {
                $(this).text('blured');
            });

            $('#clicker').mouseover(function() {
                $(this).text('mouse overed');
            });

            $('.elements input.input.first').keydown(function(ev) {
                $('.text-event').text('key downed:' + ev.altKey * 1);
            });

            $('.elements input.input.second').keypress(function(ev) {
                $('.text-event').text('key pressed:' + ev.which + ' / ' + ev.altKey * 1);
            });

            $('.elements input.input.third').keyup(function(ev) {
                $('.text-event').text('key upped:' + ev.which + ' / ' + ev.altKey * 1);
            });
		});
	</script>
</body>
</html>