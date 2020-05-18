<?php

    if ( (isset($_GET['accounts']) && $_GET['accounts'] == "true")
			|| (isset($_GET['selective']) && $_GET['selective'] == "true")
			|| (isset($_GET['import']) && $_GET['import'] == "true")
		)
        $tagpage = "accounts";
    else if ( isset($_GET['options']) && $_GET['options'] == "true" )
        $tagpage = "options";
    else if ( isset($_GET['help']) && $_GET['help'] == "true" )
        $tagpage = "help";
    else
        $tagpage = "default";

?>

<div id="zp-Metazot-Navigation">

    <div id="zp-Icon">
        <img src="<?php echo METAZOT_PLUGIN_URL; ?>/images/icon-64x64.png" title="Zotero + WordPress = Metazot">
    </div>

    <div class="nav">
        <a class="nav-item <?php if ($tagpage == "default") echo "nav-tab-active"; ?>" href="admin.php?page=Metazot"><?php _e('Browse','metazot'); ?></a>
        <?php if ( current_user_can('edit_others_posts') ) { ?><a class="nav-item <?php if ($tagpage == "accounts") echo "nav-tab-active"; ?>" href="admin.php?page=Metazot&amp;accounts=true"><?php _e('Accounts', 'metazot'); ?></a><?php } ?>
        <?php if ( current_user_can('edit_others_posts') ) { ?><a class="nav-item <?php if ($tagpage == "options") echo "nav-tab-active"; ?>" href="admin.php?page=Metazot&amp;options=true"><?php _e('Options', 'metazot'); ?></a><?php } ?>
        <a class="nav-item <?php if ($tagpage == "help") echo "nav-tab-active"; ?>" href="admin.php?page=Metazot&amp;help=true"><?php _e('Help', 'metazot'); ?></a>
    </div>

</div><!-- #zp-Metazot-Navigation -->
