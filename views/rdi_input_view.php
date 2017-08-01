<div class="module-wrapper rdi-input-module">
    <?php foreach ($nutrients as $id => $nutrient) { ?>
    <div class="input-row">
        <div class="name_en"><?=$nutrient['name_en']?></div>
        <div>
            <input type="text" class="name_sr" value="<?=(isset($nutrient['name_sr'])) ? $nutrient['name_sr'] : ''?>" />
            <input type="text" class="rdi" value="<?=(isset($nutrient['rdi'])) ? $nutrient['rdi'] : ''?>" />
            <span class="unit"><?=$nutrient['unit']?></span>
            <button class="btn btn-success save" data-id="<?=$nutrient['nid']?>">Zapamti</button>
        </div>
    </div>
    <?php } ?>
</div>