<select id="food-groups">
    <?php
    foreach ($foodGroups as $key => $group): ?>
    <option value="<?=$key?>"><?=$group?></option>
    <?php endforeach; ?>
</select>