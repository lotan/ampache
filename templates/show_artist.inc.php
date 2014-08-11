<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright 2001 - 2014 Ampache.org
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

$web_path = AmpConfig::get('web_path');
$biography = Recommendation::get_artist_info($artist->id);
$show_direct_play = AmpConfig::get('directplay');
$show_playlist_add = Access::check('interface', '25');
$directplay_limit = AmpConfig::get('direct_play_limit');

if ($directplay_limit > 0) {
    $show_playlist_add = ($artist->songs <= $directplay_limit);
    if ($show_direct_play) {
        $show_direct_play = $show_playlist_add;
    }
}
?>
<div class="details-container">
    <div class="artist-details-row details-row">
        <div class="details-title-container">
            <h1 class="item-title"><?php echo $artist->f_full_name; ?></h1>
        </div>
        <div class="details-metadata-container">
            <div class="metadata-right pull-right">
                <div class="metadata-tags">
                    <?php
                        echo $artist->f_tags;
                    ?>
                </div>
            </div>
            <p class="metadata-labels">
                <?php show_rating($artist->id, 'artist'); ?>
            </p>
            <div class="summary-container">
                <div class="summary">
                    <p class="item-summary metadata-summary" style="max-height: 72px;">
                        <?php echo $biography['summary']; ?>
                    </p>
                    <div class="summary-divider">
                        <button type="button" class="summary-divider-btn"><?php echo T_("More"); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="album-list-container details-list-container">
            <div class="list album-list">
                <div class="tabs_wrapper">
                    <div id="tabs_container">
                        <ul id="tabs">
                            <li class="tab_active"><a href="#albums"><?php echo T_('Albums'); ?></a></li>
                            <?php if (AmpConfig::get('wanted')) { ?>
                            <li><a id="missing_albums_link" href="#missing_albums"><?php echo T_('Missing Albums'); ?></a></li>
                            <?php } ?>
                            <?php if (AmpConfig::get('show_similar')) { ?>
                            <li><a id="similar_artist_link" href="#similar_artist"><?php echo T_('Similar Artists'); ?></a></li>
                             <?php } ?>
                            <?php if (AmpConfig::get('show_concerts')) { ?>
                            <li><a id="concerts_link" href="#concerts"><?php echo T_('Events'); ?></a></li>
                            <?php } ?>
                            <!-- Needed to avoid the 'only one' bug -->
                            <li></li>
                        </ul>
                    </div>
                    <div id="tabs_content">
                        <div id="albums" class="tab_content" style="display: block;">
                        <?php
                            if (!isset($multi_object_ids)) {
                                $multi_object_ids = array('' => $object_ids);
                            }

                            foreach ($multi_object_ids as $key => $object_ids) {
                                $title = (!empty($key)) ? ucwords($key) : '';
                                $browse = new Browse();
                                $browse->set_type($object_type);
                                if (!empty($key)) {
                                    $browse->set_content_div_ak($key);
                                }
                                $browse->show_objects($object_ids, array('group_disks' => true, 'title' => $title));
                                $browse->store();
                            }
                        ?>
                        </div>
                        <?php
                        if (AmpConfig::get('wanted')) {
                            echo Ajax::observe('missing_albums_link','click', Ajax::action('?page=index&action=wanted_missing_albums&artist='.$artist->id, 'missing_albums'));
                        ?>
                        <div id="missing_albums" class="tab_content">
                        <?php UI::show_box_top(T_('Missing Albums'), 'info-box'); echo T_('Loading...'); UI::show_box_bottom(); ?>
                        </div>
                        <?php } ?>
                        <?php
                        if (AmpConfig::get('show_similar')) {
                            echo Ajax::observe('similar_artist_link','click', Ajax::action('?page=index&action=similar_artist&artist='.$artist->id, 'similar_artist'));
                        ?>
                        <div id="similar_artist" class="tab_content">
                        <?php UI::show_box_top(T_('Similar Artists'), 'info-box'); echo T_('Loading...'); UI::show_box_bottom(); ?>
                        </div>
                        <?php } ?>
                        <?php
                        if (AmpConfig::get('show_concerts')) {
                            echo Ajax::observe('concerts_link','click', Ajax::action('?page=index&action=concerts&artist='.$artist->id, 'concerts'));
                        ?>
                        <div id="concerts" class="tab_content">
                        <?php UI::show_box_top(T_('Events'), 'info-box'); echo T_('Loading...'); UI::show_box_bottom(); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>    
        </div>
        <div class="details-poster-container">
            <a class="media-poster-container" href="#">
                <div class="artist-poster media-poster" style="background-image: url(<?php echo $biography['largephoto']; ?>);">
                    <div class="media-poster-overlay"></div>
                    <div class="media-poster-actions">
                        <button class="play-btn media-poster-btn btn-link" tabindex="-1">
                            <i class="fa fa-play fa-lg">
                                <a rel="nohtml" href="<?php echo AmpConfig::get('ajax_url') . '?page=stream&action=directplay&object_type=artist&object_id=' . $artist->id; ?>"></a>
                            </i></button>
                        <button class="edit-btn media-poster-btn btn-link" tabindex="-1">
                            <i class="fa fa-pencil fa-lg">
                                <?php if (Access::check('interface','50')) { ?>
                                <a rel="nohtml" id="<?php echo 'edit_artist_'.$artist->id ?>" onclick="showEditDialog('artist_row', '<?php echo $artist->id ?>', '<?php echo 'edit_artist_'.$artist->id ?>', '<?php echo T_('Artist edit') ?>', '')">
                                </a>
                                <?php } else { ?>
                                <a rel="nohtml" class="disabled" href="#"></a>
                                <?php } ?>
                            </i>
                        </button>
                        <button class="more-btn media-poster-btn btn-link" tabindex="-1">
                            <i class="fa fa-ellipsis-h fa-lg">
                            </i>
                        </button>
                    </div>
                    <?php 
                        if (AmpConfig::get('show_played_times')) {
                            echo '<span class="unwatched-count-badge badge badge-lg">'.$artist->object_cnt.'</span>';
                        }
                    ?>
                </div>
            </a>
            <div class="media-actions-dropdown dropdown" style="top: 96px; left: 96px;">
                <div class="dropdown-toggle" data-toggle="dropdown"></div>
                <ul class="dropdown-menu">
                    <li>
                        <?php if ($object_type == 'album') { ?>
                        <a rel="nohtml" class="show-all-songs-btn" href="<?php echo $web_path; ?>/artists.php?action=show_all_songs&amp;artist=<?php echo $artist->id; ?>">
                            <?php echo T_("Show all"); ?>
                        </a>
                        <?php } else { ?>
                        <a rel="nohtml" class="show-all-albums-btn" href="<?php echo $web_path; ?>/artists.php?action=show&amp;artist=<?php echo $artist->id; ?>">
                            <?php echo T_("Show albums"); ?>
                        </a>
                        <?php } ?>
                    </li>
                    <?php if (Stream_Playlist::check_autoplay_append()) { ?>
                    <li>
                        <a rel="nohtml" class="add-to-up-next-btn" href="<?php echo AmpConfig::get('ajax_url') . '?page=stream&action=directplay&object_type=artist&object_id=' . $artist->id . '&append=true'; ?>" tabindex="-1">
                            <?php echo T_('Play next'); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li>
                        <a rel="nohtml" class="add-to-playlist-btn" href="<?php echo AmpConfig::get('ajax_url') . '?action=basket&type=artist&id=' . $artist->id; ?>" tabindex="-1">
                            <?php echo T_('Add to temporary playlist'); ?>
                        </a>
                    </li>
                    <li>
                        <a rel="nohtml" class="random-to-playlist-btn" href="<?php echo AmpConfig::get('ajax_url') . '?action=basket&type=artist_random&id=' . $artist->id; ?>" tabindex="-1">
                            <?php echo T_('Random to temporary playlist'); ?>
                        </a>
                    </li>
                    
                    <li class="divider"></li>
                    
                    <?php if (Access::check_function('batch_download')) { ?>
                    <li>
                        <a rel="nohtml" class="add-to-up-next-btn" href="<?php echo $web_path; ?>/batch.php?action=artist&id=<?php echo $artist->id; ?>" tabindex="-1">
                            <?php echo T_('Download'); ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
if (AmpConfig::get('lastfm_api_key')) {
    //echo Ajax::observe('window', 'load', Ajax::action('?page=index&action=artist_info&artist='.$artist->id, 'artist_info'));
?>
    <div class="item_right_info">
        <div class="external_links">
            <a href="http://www.google.com/search?q=%22<?php echo rawurlencode($artist->f_name); ?>%22" target="_blank"><?php echo UI::get_icon('google', T_('Search on Google ...')); ?></a>
            <a href="http://en.wikipedia.org/wiki/Special:Search?search=%22<?php echo rawurlencode($artist->f_name); ?>%22&go=Go" target="_blank"><?php echo UI::get_icon('wikipedia', T_('Search on Wikipedia ...')); ?></a>
            <a href="http://www.last.fm/search?q=%22<?php echo rawurlencode($artist->f_name); ?>%22&type=artist" target="_blank"><?php echo UI::get_icon('lastfm', T_('Search on Last.fm ...')); ?></a>
        </div>
        <div id="artist_biography">
            <?php echo T_('Loading...'); ?>
        </div>
    </div>
<?php } ?>


<?php
if (AmpConfig::get('show_played_times')) {
?>
<br />
<div style="display:inline;"><?php echo T_('Played') . ' ' . $artist->object_cnt . ' ' . T_('times'); ?></div>
<?php
}
?>
<div id="information_actions">
    <h3><?php echo T_('Actions'); ?>:</h3>
    <ul>
        <li>
            <?php if ($object_type == 'album') { ?>
            <a href="<?php echo $web_path; ?>/artists.php?action=show_all_songs&amp;artist=<?php echo $artist->id; ?>">
            <?php echo UI::get_icon('view', T_("Show all")); ?></a>
            <a href="<?php echo $web_path; ?>/artists.php?action=show_all_songs&amp;artist=<?php echo $artist->id; ?>">
                <?php echo T_("Show all"); ?>
            </a>
            <?php } else { ?>
            <a href="<?php echo $web_path; ?>/artists.php?action=show&amp;artist=<?php echo $artist->id; ?>">
            <?php echo UI::get_icon('view', T_("Show albums")); ?></a>
            <a href="<?php echo $web_path; ?>/artists.php?action=show&amp;artist=<?php echo $artist->id; ?>">
            <?php echo T_("Show albums"); ?></a>
            <?php } ?>
        </li>
        <?php if ($show_direct_play) { ?>
        <li>
            <?php echo Ajax::button('?page=stream&action=directplay&object_type=artist&object_id=' . $artist->id,'play', T_('Play all'),'directplay_full_' . $artist->id); ?>
            <?php echo Ajax::text('?page=stream&action=directplay&object_type=artist&object_id=' . $artist->id, T_('Play all'),'directplay_full_text_' . $artist->id); ?>
        </li>
            <?php if (Stream_Playlist::check_autoplay_append()) { ?>
        <li>
            <?php echo Ajax::button('?page=stream&action=directplay&object_type=artist&object_id=' . $artist->id . '&append=true','play_add', T_('Play all last'),'addplay_artist_' . $artist->id); ?>
            <?php echo Ajax::text('?page=stream&action=directplay&object_type=artist&object_id=' . $artist->id . '&append=true', T_('Play all last'),'addplay_artist_text_' . $artist->id); ?>
        </li>
            <?php } ?>
        <?php } ?>
        <?php if ($show_playlist_add) { ?>
        <li>
            <?php /* HINT: Artist Fullname */ ?>
            <?php echo Ajax::button('?action=basket&type=artist&id=' . $artist->id,'add', T_('Add all to temporary playlist'),'add_' . $artist->id); ?>
            <?php echo Ajax::text('?action=basket&type=artist&id=' . $artist->id, T_('Add all to temporary playlist'),'add_text_' . $artist->id); ?>
        </li>
        <li>
            <?php /* HINT: Artist Fullname */ ?>
            <?php echo Ajax::button('?action=basket&type=artist_random&id=' . $artist->id,'random', T_('Random all to temporary playlist'),'random_' . $artist->id); ?>
            <?php echo Ajax::text('?action=basket&type=artist_random&id=' . $artist->id, T_('Random all to temporary playlist'),'random_text_' . $artist->id); ?>
        </li>
        <?php } ?>
        <?php if (Access::check('interface','25')) { ?>
            <?php if (AmpConfig::get('sociable')) { ?>
                <a href="<?php echo AmpConfig::get('web_path'); ?>/shout.php?action=show_add_shout&type=artist&id=<?php echo $artist->id; ?>"><?php echo UI::get_icon('comment', T_('Post Shout')); ?></a>
                <a href="<?php echo AmpConfig::get('web_path'); ?>/shout.php?action=show_add_shout&type=artist&id=<?php echo $artist->id; ?>"><?php echo T_('Post Shout'); ?></a>
            <?php } ?>
            <?php if (Access::check_function('batch_download') && check_can_zip('artist')) { ?>
            <li>
                <a rel="nohtml" href="<?php echo $web_path; ?>/batch.php?action=artist&id=<?php echo $artist->id; ?>"><?php echo UI::get_icon('batch_download', T_('Download')); ?></a>
                <a rel="nohtml" href="<?php echo $web_path; ?>/batch.php?action=artist&id=<?php echo $artist->id; ?>"><?php echo T_('Download'); ?></a>
            </li>
            <?php } ?>
        <?php } ?>
        <?php if (Access::check('interface','50')) { ?>
            <?php if (AmpConfig::get('statistical_graphs')) { ?>
                <li>
                    <a href="<?php echo AmpConfig::get('web_path'); ?>/stats.php?action=graph&object_type=artist&object_id=<?php echo $artist->id; ?>"><?php echo UI::get_icon('statistics', T_('Graphs')); ?></a>
                    <a href="<?php echo AmpConfig::get('web_path'); ?>/stats.php?action=graph&object_type=artist&object_id=<?php echo $artist->id; ?>"><?php echo T_('Graphs'); ?></a>
                </li>
            <?php } ?>
            <a id="<?php echo 'edit_artist_'.$artist->id ?>" onclick="showEditDialog('artist_row', '<?php echo $artist->id ?>', '<?php echo 'edit_artist_'.$artist->id ?>', '<?php echo T_('Artist edit') ?>', '')">
                <?php echo UI::get_icon('edit', T_('Edit')); ?>
            </a>
            <a id="<?php echo 'edit_artist_'.$artist->id ?>" onclick="showEditDialog('artist_row', '<?php echo $artist->id ?>', '<?php echo 'edit_artist_'.$artist->id ?>', '<?php echo T_('Artist edit') ?>', '')">
                <?php echo T_('Edit Artist'); ?>
            </a>
        <?php } ?>
    </ul>
</div>
<?php UI::show_box_bottom(); ?>
<div class="tabs_wrapper">
    <div id="tabs_container">
        <ul id="tabs">
            <li class="tab_active"><a href="#albums"><?php echo T_('Albums'); ?></a></li>
<?php if (AmpConfig::get('wanted')) { ?>
            <li><a id="missing_albums_link" href="#missing_albums"><?php echo T_('Missing Albums'); ?></a></li>
<?php } ?>
<?php if (AmpConfig::get('show_similar')) { ?>
            <li><a id="similar_artist_link" href="#similar_artist"><?php echo T_('Similar Artists'); ?></a></li>
<?php } ?>
<?php if (AmpConfig::get('show_concerts')) { ?>
            <li><a id="concerts_link" href="#concerts"><?php echo T_('Events'); ?></a></li>
<?php } ?>
            <!-- Needed to avoid the 'only one' bug -->
            <li></li>
        </ul>
    </div>
    <div id="tabs_content">
        <div id="albums" class="tab_content" style="display: block;">
<?php
    if (!isset($multi_object_ids)) {
        $multi_object_ids = array('' => $object_ids);
    }

    foreach ($multi_object_ids as $key => $object_ids) {
        $title = (!empty($key)) ? ucwords($key) : '';
        $browse = new Browse();
        $browse->set_type($object_type);
        if (!empty($key)) {
            $browse->set_content_div_ak($key);
        }
        $browse->show_objects($object_ids, array('group_disks' => true, 'title' => $title));
        $browse->store();
    }
?>
        </div>
<?php
if (AmpConfig::get('wanted')) {
    echo Ajax::observe('missing_albums_link','click', Ajax::action('?page=index&action=wanted_missing_albums&artist='.$artist->id, 'missing_albums'));
?>
        <div id="missing_albums" class="tab_content">
        <?php UI::show_box_top(T_('Missing Albums'), 'info-box'); echo T_('Loading...'); UI::show_box_bottom(); ?>
        </div>
<?php } ?>
<?php
if (AmpConfig::get('show_similar')) {
    echo Ajax::observe('similar_artist_link','click', Ajax::action('?page=index&action=similar_artist&artist='.$artist->id, 'similar_artist'));
?>
        <div id="similar_artist" class="tab_content">
        <?php UI::show_box_top(T_('Similar Artists'), 'info-box'); echo T_('Loading...'); UI::show_box_bottom(); ?>
        </div>
<?php } ?>
<?php
if (AmpConfig::get('show_concerts')) {
    echo Ajax::observe('concerts_link','click', Ajax::action('?page=index&action=concerts&artist='.$artist->id, 'concerts'));
?>
        <div id="concerts" class="tab_content">
        <?php UI::show_box_top(T_('Events'), 'info-box'); echo T_('Loading...'); UI::show_box_bottom(); ?>
        </div>
<?php } ?>
    </div>
</div>
