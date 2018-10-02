{include file="partials/header.html.tpl"}
{include file="player2.html.tpl" player_id="1" playlist=$playlist_json}
<script type="text/javascript">
    $(document).ready(function() {
        $('.inlinesparkline').sparkline();
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        {include file="player2.js.tpl" player_id="1" playlist=$playlist_json}
    });
</script>
{include file="partials/footer.html.tpl"}