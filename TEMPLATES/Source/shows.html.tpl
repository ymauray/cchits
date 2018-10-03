{extends file="partials/_default.html.tpl"}
{block name="content"}
    <div class="container">
        <div class="row row-header">
            <div class="col-12">
                <header>{$page_title}</header>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                {include file="player2.html.tpl" player_id="1" playlist=$playlist_json playlist_is_array=true}
            </div>
            <div class="col-12 col-md-6">
                {foreach from=$shows key=id item=show}
                    <div class="row">
                        <div class="col-12">
                            <!--h3><a href="{$baseURL}show/{$show.intShowID}">{$show.strShowName}</a></h3-->
                            {foreach from=$show.arrTracks item=track}
                                <form action="{$baseURL}vote/{$track.intTrackID}?go" method="post">
                                    <div>"<a href="{$track.strTrackUrl}">{$track.strTrackName}</a>" by "<a href="{$track.strArtistUrl}">{$track.strArtistName}</a>"</div>
                                    <div><input type="submit" name="go" value="I like this track!" /></div>
                                </form>
                            {/foreach}
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
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
{/block}
