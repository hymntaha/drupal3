<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<script type="text/javascript">
    var homepage_video = document.getElementsByTagName('video');
    if (homepage_video.length > 0) {
        var video_height = homepage_video[0].clientHeight;
        var content_height = document.getElementsByClassName('full-screen-content-wrapper')[0].clientHeight;
        var height_diff = video_height - content_height;

        if (height_diff > 0) {
            var video_wrapper = document.getElementsByClassName('field-name-field-video')[0];
            video_wrapper.style.marginTop = height_diff * -1 + 'px';
            video_wrapper.style.visibility = 'visible';
        }
    }
</script>
<article id="node-<?php print $node->nid; ?>-<?= $view_mode ?>"
         class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
    <?php if ((!$page && !empty($title)) || !empty($title_prefix) || !empty($title_suffix) || $display_submitted): ?>
        <header>
            <?php print render($title_prefix); ?>
            <?php if (!$page && !empty($title)): ?>
                <h2<?php print $title_attributes; ?>><a
                            href="<?php print $node_url; ?>"><?php print $title; ?></a>
                </h2>
            <?php endif; ?>
            <?php print render($title_suffix); ?>
            <?php if ($display_submitted): ?>
                <span class="submitted">
      <?php print $user_picture; ?>
      <?php print $submitted; ?>
    </span>
            <?php endif; ?>
        </header>
    <?php endif; ?>
    <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_tags']);
    hide($content['field_packages']);
    hide($content['field_testimonials']);
    hide($content['body']);
    hide($content['field_content_containers']);
    hide($content['field_mobile_content_containers']);
    ?>
    <div class="hidden-xs">
        <div class="homepage-section intro-section">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo render($content['field_intro_title']); ?>
                    </div>
                    <div class="col-xs-12">
                        <?php echo render($content['field_intro_text']); ?>
                    </div>
                    <div class="col-xs-12 text-center featured-content">

                        <div class="row">
                            <div class="hidden-xs col-sm-3">
                                <h5 style="text-align: left;"><?php echo t('Promotions') ?>
                                    <h5>
                            </div>
                            <div class="hidden-xs col-sm-9">
                                <h5 style="text-align: left;"><?php echo t('Featured Tours') ?></h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="hidden-xs col-sm-3">
                                <?php print render($content['field_slideshow']) ?></div>
                            <?php
                            foreach (element_children($content['field_featured_packages']) as $pkg): ?>
                                <div class="hidden-xs col-sm-3">
                                <?php print render($content['field_featured_packages'][$pkg]);
                                ?></div><?php
                            endforeach;
                            ?></div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-sm-offset-2 text-center">
                        <?php echo l(t('View All Bus Tours') . '<span class="right-arrow"> &rarr; <span>', 'nyc-bus-tours', array('html' => TRUE)) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="homepage-section body-section">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <?php print render($content['field_body_title']); ?>
                    </div>
                    <div class="col-sm-6">
                        <?php print render($content['body']); ?>
                    </div>
                    <div class="col-sm-6">
                        <?php print render($content['field_body_right']); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="homepage-section show-city-section">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <?php print render($content['field_show_city_title']); ?>
                        <?php print render($content['field_show_city_images']); ?>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-sm-offset-2 text-center">
                        <?php print render($content['field_show_city_text']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="visible-xs">
        <?php print render($content['field_mobile_content_containers']); ?>
        <h4 class="text-center">Featured Tours</h4>
        <?php foreach (element_children($content['field_featured_packages']) as $pkg): ?>
            <?php print render($content['field_featured_packages'][$pkg]); ?>
        <?php endforeach; ?>
        <div class="text-center">
            <?php echo l(t('View All Bus Tours') . '<span class="right-arrow"> &rarr; <span>', 'nyc-bus-tours',
                    array('html' => TRUE)) ?>
        </div>
    </div>

</article>
