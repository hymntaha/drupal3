<div class="leaflet-control-layers-separator" style="display:block;"></div>
<div class="leaflet-control-layers-extra">
	<?php foreach($control_links as $link):?>
		<label>
			<input type="radio" name="leaflet-control-layers-extra-input" value="<?=$link['href']?>" <?=$link['checked']?> />
			<span><?=$link['title']?></span>
		</label>
	<?php endforeach;?>
</div>