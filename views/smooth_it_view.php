<div class="module-wrapper smooth-it-module">
<h3>Smooth it!</h3>
    <div class="foods-container">
        <div class="indi-food-container">
            <div class="input-row which-food">
                <select class="food-select">
                    <option></option>
                    <?php
                    foreach ($foods as $key => $food) { ?>
                        <option data-refuse="<?=$food['refuse']?>" data-price="<?=$food['price']?>" value="<?=$food['id']?>" ><?=$food['name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="food-params hidden">
                <div class="input-row">
                    <label for="weight">Težina (g)</label><br>
                    <input type="number" class="weight" min="0" />
                </div>
                <div class="input-row">
                    <div class="half">
                        <label for="price">Cena (rsd)</label><br>
                        <input type="number" class="price" min="0" />
                    </div>
                    <div class="half">
                        <label for="refuse">Procenat otpatka (%)</label><br>
                        <input type="text" class="refuse" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="submit-row submit-container">
        <button id="add" class="btn btn-primary">Dodaj hranu</button>
        <button id="calc" class="btn btn-success">Izračunaj</button>
    </div>
</div>

<div id="template" class="hidden">
    <div class="indi-food-container">
        <div class="input-row which-food">
            <select class="food-select template">
                <option></option>
				<?php
				foreach ($foods as $key => $food) { ?>
                    <option data-refuse="<?=$food['refuse']?>" data-price="<?=$food['price']?>" value="<?=$food['id']?>" ><?=$food['name']?></option>
				<?php } ?>
            </select>
        </div>
        <div class="food-params hidden">
            <div class="input-row">
                <label for="weight">Težina (g)</label><br>
                <input type="number" class="weight" min="0" />
            </div>
            <div class="input-row">
                <div class="half">
                    <label for="price">Cena (rsd)</label><br>
                    <input type="number" class="price" min="0" />
                </div>
                <div class="half">
                    <label for="refuse">Procenat otpatka (%)</label><br>
                    <input type="text" class="refuse" />
                </div>
            </div>
        </div>
    </div>
</div>