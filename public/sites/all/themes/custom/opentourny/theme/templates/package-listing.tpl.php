<div id="package-listing">
    <?php foreach($packages as $tid => $term):?>
        <div class="term">
            <div class="row">
                <div class="col-xs-12">
                    <hr />
                    <?=render($term['term']);?>
                </div>
                <?php foreach($term['packages'] as $package):?>
                    <div class="col-xs-12 col-sm-6">
                        <?=render($package);?>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    <?php endforeach;?>
</div>
