<div class="module-wrapper indi-calc-module">
<div class="food-select-row">
    <label for="foods">Hrana</label><br>
    <select id="foods" class="food-select">
        <option></option>
        <?php
        foreach ($foods as $key => $food) { ?>
        <option data-refuse="<?=$food['refuse']?>" data-price="<?=$food['price']?>" value="<?=$food['id']?>" ><?=$food['name']?></option>
        <?php } ?>
    </select>
</div>

<div id="food-details">
    <div class="input-row">
        <label for="weight">Težina (g)</label><br>
        <input type="number" id="weight" min="0" autofocus />
    </div>
    <div class="input-row">
        <div class="half">
            <label for="price">Cena (rsd)</label><br>
            <input type="number" id="price" min="0" />
        </div>
        <div class="half">
            <label for="refuse">Procenat otpatka (%)</label><br>
            <input type="text" id="refuse" />
        </div>
    </div>
    <div class="input-row">
        <button id="calc" class="btn btn-primary">Izračunaj</button>
    </div>
</div>
</div>