<div class="food-select-row food-groups-row">
    <label for="food-groups">Grupe hrane</label><br>
    <select id="food-groups" class="food-select">
        <option></option>
        <?php
        foreach ($foodGroups as $key => $group) { ?>
        <option value="<?=$key?>" <?= (isset($group_id) && $key == $group_id) ? 'selected="selected"' : ''?>><?=$group?></option>
        <?php } ?>
    </select>
</div>
<div class="food-select-row food-items-row">
    <label for="food-items">Pojedinačna hrana</label><br>
    <select id="food-items" class="food-select">
		<option></option>
        <?php
		foreach ($foodItems as $key => $food) { ?>
            <option value="<?=$key?>" <?= (isset($food_id) && $key == $food_id) ? 'selected="selected"' : ''?>><?= (in_array($key, $existingFood)) ? '* ' : '' ?><?=$food?></option>
		<?php } ?>
    </select>
</div>

<fieldset id="food-details">
    <legend>Unos hrane:</legend>
    <div class="input-row">
        <label for="fid">ID</label><br>
        <input type="text" id="fid" name="fid" readonly value="<?=$thatFood['fid']?>" />
    </div>
    <div class="input-row">
        <label for="name_sr">Naziv (srpski) *</label><br>
        <input type="text" id="name_sr" name="name_sr" autofocus value="<?=(isset($thatFood['name_sr'])) ? $thatFood['name_sr'] : ''?>" />
    </div>
    <div class="input-row">
        <label for="name_en">Naziv (engleski)</label><br>
        <input type="text" id="name_en" name="name_en" readonly value="<?=$thatFood['name_en']?>" />
    </div>
    <div class="input-row">
        <label for="price">Cena (rsd)</label><br>
        <input type="number" id="price" name="price" min="0" value="<?=(isset($thatFood['price'])) ? $thatFood['price'] : ''?>" />
    </div>
    <div class="input-row">
        <label for="refuse">Procenat otpatka (%)</label><br>
        <input type="text" id="refuse" name="refuse" value="<?=$thatFood['refuse']?>" />
    </div>
    <input type="hidden" id="unit" name="unit" value="<?=$thatFood['unit']?>" />
    <input type="hidden" id="data" name="data" value='<?=$thatFood['data']?>' />
    <button id="save" class="btn btn-success">Zapamti</button>
</fieldset>